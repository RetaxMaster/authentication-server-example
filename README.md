# Servidor de autenticación

Esta es una practica de un servidor de autenticación usando los flujos de **OAuth 2.0** con Laravel Passport y Laravel Jetstream (Livewire).

Este servidor está pensado para trabajar en sistemas distribuidos como un servidor de autenticación independiente, el API debe estar en otro servidor, por lo que el servidor del API debe llamar al endpoint de verificación de tokens de este servidor.

## Flujos implementados

Por ahora, este servidor soporta los sigueientes flujos:

- Password Grant Tokens
- Authorization Code Grant

Para el flujo de Password Grant Tokens, la lógica fue definida en la rama `password-grant-tokens`, esto para hacer la práctica de únicamente permitir registro de usuarios pero no login dentro de este servidor (porque para el flujo de Password Grant Tokens la aplicación debe mandar las credenciales del usuario, aquí realmente no importa que el usuario pueda loguearse ya que le abstraemos la información de la lista de los tokens que se le han emitido)

Para el resto de flujo, los manejamos en la rama `main`, esta sí maneja todo el sistema de registro, login y recuperación de contraseña (provisto por Jetstream con Livewire), ya que aquí si necesitamos que el usuario pueda loguearse para gestionar sus aplicaciones (además de mostrarle la página de autorización cuando una aplicación requiera su acceso)

## Cómo usar el proyecto

Simplemente se debe clonar y elegir cuál rama usar, cada vez que se cambie de rama se necesita reinstalar todas las dependencias ya que son proyectos diferentes, también se deben reejecutar todas las migraciones y el comando de `passport:install` para obtener los clientes actualizados. Passport, a través de la terminal proveerá el Client Id y el Client Secret  para pruebas. La rama del Password Grant Tokens incluye tests para rpobar todo el flujo de obtención de dichos tokens, sirve como guía para usar el servidor.

Los demás flujos incluyen tests para la creación de clientes, su uso está en la [documentación de Laravel Passport](https://laravel.com/docs/8.x/passport).

Para los flujos de Authorization Code Grant se recomienda registrar un usuario desde su endpoint de registro, y una vez registrado crear un cliente para él usando Laravel Tinker:

```
$ php artisan tinker
Psy Shell v0.10.6 (PHP 8.0.3 — cli) by Justin Hileman
>>> use Laravel\Passport\Client;
>>> $client = Client::factory()->create([
... "user_id" => 1,
... "redirect" => "http://127.0.0.2:8000/callback"
... ]);
=> Laravel\Passport\Client {#3616
     user_id: 1,
     name: "Swift Group",
     redirect: "http://127.0.0.2:8000/callback",
     personal_access_client: false,
     password_client: false,
     revoked: false,
     id: "92ef7af6-8a90-4d48-ba00-0db4ed2d30bd",
     updated_at: "2021-03-12 18:09:38",
     created_at: "2021-03-12 18:09:38",
   }
>>> $client->plainSecret;
=> "Yu6AZhaY526F4VAPNSGmv86vAlgsQCmNjqClDAG0"
>>> 
```

Es importante que el `redirect` contenga la URL de redirección exacta para ese cliente y que NO sea la misma IP/Dominio que la del servidor de autenticación.

En cualquier caso, el `redirect` se puede cambiar desde la base de datos en la tabla `oauth_clients`

Esto retornará el Client Id:
```
=> Laravel\Passport\Client {#3616
     ...
     id: "92ef7af6-8a90-4d48-ba00-0db4ed2d30bd",
     ...
   }
```
Y el Client Secret:

```
>>> $client->plainSecret;
=> "Yu6AZhaY526F4VAPNSGmv86vAlgsQCmNjqClDAG0"
```

**Esta es la única vez que tendrás acceso a estas credenciales, así que no las pierdas.**

## Aplicaciones de prueba

Para probar el flujo de **Authorization Code Grant** tenemos este proyecto de Laravel que se comunica con los endpoints de este servidor de autenticación:

[RetaxMaster/application-for-authorization-code-grant](https://github.com/RetaxMaster/application-for-authorization-code-grant)