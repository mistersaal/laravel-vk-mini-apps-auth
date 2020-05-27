# Аутентификация пользователя в VkMiniApps для Laravel
## Установка

```
composer require mistersaal/laravel-vk-mini-apps-auth

php artisan vendor:publish --provider=Mistersaal\VkMiniAppsAuth\VkMiniAppsAuthServiceProvider
```

В .env файл добавить:
```dotenv
VK_SECRET="your_secret_key"
VK_TOKEN="your_service_token"
```

В App\Http\Kernel.php в $routeMiddleware добавить:
```
'auth.vk' => VkMiniAppsAuthenticate::class,
```

В config\auth.php:

- defaults.guard => 'vkMiniApps'
- в массив guards добавить:
```
'vkMiniApps' => [
    'driver' => 'vkSign',
    'provider' => 'vkUsers',
],
```
- в массив providers добавить:
```
'vkUsers' => [
    'driver' => 'vkMiniApps',
    'model' => App\User::class,
],
```

## Использование

Теперь можно использовать middleware *'auth.vk'*,
который будет пропускать только аутентифицированных
пользователей Vk Mini Apps (!!! он не пропустит
пользователя, которого еще нет в вашей базе !!!)

URL с подписью передавайте с каждым запросом в заголовке 'X-Vk-Auth-Url' (либо измените в config/vkminiapps.php).
Пример для axios:
```javascript
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Vk-Auth-Url'] = window.location.href;
```

Для автоматической регистрации пользователя можно
сделать вот такой контроллер, который не будет
защищен мидлваром:
```php
class LoginController extends Controller
{
    /**
     * @param VkUsersData $vkUsersData Класс для получения пользователя с данными по апи (сами реализуете как вам надо)
     *@return array
    */
    public function login(VkUsersData $vkUsersData)
    {
        //Если подпись верна и пользователь уже есть, то вернет true
        //Если пользователя нет в базе, то false
        //Если ошибка подписи, то выбросит VkSignException (можно не отлавливать, пользователь просто получит 500)
        if (auth()->validate()) {
            $user = auth()->user();
            $vkUsersData->updateUserData($user);
            $user->save();
            return ['success' => true, 'newUser' => false];
        } else {
            $user = $vkUsersData->getNewUser();
            $user->save();
            return ['success' => true, 'newUser' => false];
        }
    }
}
```