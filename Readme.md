Cirici API Bundle
=================

User access API to allow users to login against.

The authentication is made with [oauth2 specification](http://tools.ietf.org/html/rfc6749)
The bundle used to handle oauth2 authentication is [FOSOAuthServerBundle](https://github.com/FriendsOfSymfony/FOSOAuthServerBundle)

A good starting point to understand the authentication proces is to check this tutorial: [OAuth2 explained](http://blog.tankist.de/blog/2013/07/16/oauth2-explained-part-1-principles-and-terminology/)


Installation
------------

In order to use this bundle you have to add the next lines to config.yml

```yaml
# FOSUser configs
fos_user:
    db_driver: orm
    firewall_name: api
    user_class: Cirici\ApiBundle\Entity\User
    from_email:
        address:              webmaster@api.davantis.cirici.com
        sender_name:          webmaster
    resetting:
        token_ttl:            21600 # 6 hours i guess
        email:
            template:             'FOSUserBundle:Resetting:email.txt.twig'
            from_email:
                address:              webmaster@api.davantis.cirici.com
                sender_name:          webmaster

# FOSRest configs
fos_rest:
    param_fetcher_listener: true
    view:
        view_response_listener: force
    routing_loader:
        default_format: json
    serializer:
        serialize_null: true

# FOSOAuth configs
fos_oauth_server:
    db_driver: orm
    client_class: Cirici\ApiBundle\Entity\Client
    access_token_class: Cirici\ApiBundle\Entity\AccessToken
    refresh_token_class: Cirici\ApiBundle\Entity\RefreshToken
    auth_code_class: Cirici\ApiBundle\Entity\AuthCode
    service:
        user_provider: fos_user.user_manager
        options:
            supported_scopes: user

# In order to override the User entity you have to add the next lines
# this changes the pointer of all the relationships to user bundle
doctrine:
    orm:
        resolve_target_entities:
            Cirici\ApiBundle\Model\UserInterface: Cirici\YourBrandNewBundle\Entity\SomeUserEntity

```

You should add the next lines to ``routing.yml`` as well:

```yml
cirici_api:
    resource: "@CiriciApiBundle/Resources/config/routing.yml"
    prefix:   /

fos_oauth_server_token:
    resource: "@FOSOAuthServerBundle/Resources/config/routing/token.xml"

fos_oauth_server_authorize:
    resource: "@FOSOAuthServerBundle/Resources/config/routing/authorize.xml"

cirici_oauth_server_auth_login:
    pattern: /oauth/v2/auth_login
    defaults: { _controller: CiriciApiBundle:Security:login }

cirici_oauth_server_auth_login_check:
    pattern: /oauth/v2/auth_login_check
    defaults: { _controller: FOSUserBundle:Security:check }

```

Enabling user call in routing.yml:

```yml
cirici_oauth_server_user:
    pattern: /api/user
    defaults: { _controller: CiriciApiBundle:Api:user }
```

Create oauth2 clients
---------------------

The first thing you should create to allow users login to the API is create the Clients, the client is the types of entities
will able to authenticate against our oauth server. A client could be one for the mobile apps using the API, other for allowing access to other APIs to our API, other for web users, and so on.


```
php app/console acme:oauth-server:client:create --redirect-uri="CLIENT_HOST" --grant-type="authorization_code" --grant-type="password" --grant-type="refresh_token" --grant-type="token" --grant-type="client_credentials"
```

You can define wich grant types will accept this Client, in the example we enable all of them. But the Password Grant type is what is planned to use with this project.

When you create a client you will get the next important information (You can check it in its database table):

- public_id: The Client ID
- secret: The secret generated needed to use this Client


Getting AccessToken a.k.a login
-------------------------------

To perform the login with a web form you can check the next url:

/oauth/v2/auth_login

Or you can send those parameters:

- client_id
- client_secret
- grant_type = 'password'
- username
- password

throught POST (Or maybe headers) to the next url:

/oauth/v2/token

As a result you will get the next info:

- access_token: The info you asked for
- expires_in: The lifetime it has
- token_type: The type of the token
- scope: The scope applied to the token
- refresh_token: The RefreshToken value used to renew the AccessToken


Using RefreshToken
------------------

AccessToken has a lifetime of one hour (3600s), so each time is outdated we should ask for a new AccessToken using the RefreshToken.

To refresh the AccessToken when it expires whe should do the next:

Send those parameters:

- client_id
- client_secret
- grant_type = 'refresh_token'
- refresh_token

to:

/oauth/v2/token

As a result yo'll get the same as the AccessToken call with a new AccessToken


You can check the Tests on the project to see how are the flows.



User entity
-----------

[User inheritance](http://stackoverflow.com/questions/9801173/creating-portable-bundles-with-extendable-entities-in-symfony2)

Resetting Password
------------------

To request a reset password you should send by POST the next parameters:

- username (should be the user email or the username, in our case we will use the email address)

to:

/api/resetting/send-email

This call will send an email to specified user with the url to reset his password. This call will be a web form fit reset password form.

Problems
--------

If you have problems with Doctrine proxy classes generation you should run:

```
app/console cache:warmup --env=prod --no-debug
```
