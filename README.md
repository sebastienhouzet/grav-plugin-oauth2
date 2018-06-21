# OAuth2 Plugin

**This README.md file should be modified to describe the features, installation, configuration, and general usage of this plugin.**

The **Oauth2** Plugin is for [Grav CMS](http://github.com/getgrav/grav). Access on Front Office only after login with oAuth2.0

## Installation

Installing the Oauth2 plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install oauth2

This will install the Oauth2 plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/oauth2`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `oauth2`. You can find these files on [GitHub](https://github.com/sebastienhouzet/grav-plugin-oauth2) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/oauth2
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

### Admin Plugin

If you use the admin plugin, you can install directly through the admin plugin by browsing the `Plugins` tab and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/oauth2/oauth2.yaml` to `user/config/plugins/oauth2.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: false
clientId: ''
clientSecret: ''
redirectUri: 'http://redirectUri'
urlAuthorize: 'https://urlAuthorize/as/authorization.oauth2'
urlAccessToken: 'https://urlAccessToken/as/token.oauth2'
urlResourceOwnerDetails: 'https://urlResourceOwnerDetails/idp/userinfo.openid'
scopes: 'openid profile email'
```

Note that if you use the admin plugin, a file with your configuration, and named oauth2.yaml will be saved in the `user/config/plugins/` folder once the configuration is saved in the admin.

## Usage

You can block access to website without oAuth2 connexion.
You can also activate for admin access.

## Credits

Sébastien HOUZET - Yoozio.com

## To Do

- [ ] Disabled for front
- [ ] Manage redirect after login on page call before authentification

## Contributing

Please see [CONTRIBUTING](https://github.com/sebastienhouzet/grav-plugin-oauth2/blob/master/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](https://github.com/sebastienhouzet/grav-plugin-oauth2/blob/master/LICENSE) for more information.