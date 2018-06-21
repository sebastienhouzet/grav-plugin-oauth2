<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use GuzzleHttp\Psr7;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\Plugin;
use Grav\Common\Uri;
use Grav\Common\User\User;
use Grav\Plugin\Login\Events\UserLoginEvent;
use Grav\Plugin\Login\Login;

/**
 * Class Oauth2Plugin
 * @package Grav\Plugin
 */
class Oauth2Plugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => [
                ['autoload', 100000],
                ['onPluginsInitialized', 0]
            ],
        ];
    }

    /**
     * [onPluginsInitialized:100000] Composer autoload.
     *
     * @return ClassLoader
     */
    public function autoload()
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        if ($this->active) {
            $this->loginRedirect();
        }
    }

    public function loginRedirect()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin() && !$this->grav['config']->get('plugins.oauth2.admin')) {
            return;
        }
        
        $user = isset($this->grav['user']) ? $this->grav['user'] : null;
        if ($user && $user->authorized) {
            return;
        } else {

            // Front and Admin have two different session. It is important to have 2 redirect uri, one for front and one for admin
            $redirectUri = $this->grav['config']->get('plugins.oauth2.redirectUri');
            if($this->isAdmin() && $this->grav['config']->get('plugins.oauth2.admin')) {
                $redirectUri .= $this->config->get('plugins.admin.route');
            }

            $provider = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId'                => $this->grav['config']->get('plugins.oauth2.clientId'),
                'clientSecret'            => $this->grav['config']->get('plugins.oauth2.clientSecret'),
                'redirectUri'             => $redirectUri,
                'urlAuthorize'            => $this->grav['config']->get('plugins.oauth2.urlAuthorize'),
                'urlAccessToken'          => $this->grav['config']->get('plugins.oauth2.urlAccessToken'),
                'urlResourceOwnerDetails' => $this->grav['config']->get('plugins.oauth2.urlResourceOwnerDetails'),
                'scopes'                  => [$this->grav['config']->get('plugins.oauth2.scopes')]
            ]);

            /** @var Session $session */
            $session = $this->grav['session'];
            
            // If we don't have an authorization code then get one
            if (!isset($_GET['code'])) {

                // Fetch the authorization URL from the provider; this returns the
                // urlAuthorize option and generates and applies any necessary parameters
                // (e.g. state).
                $authorizationUrl = $provider->getAuthorizationUrl();

                // Get the state generated for you and store it to the session.
                $session->oauth2_state = $provider->getState();

                // Redirect the user to the authorization URL.
                $this->grav->redirect($authorizationUrl);
                exit;

            // Check given state against previously stored one to mitigate CSRF attack
            } elseif (empty($_GET['state']) || ($_GET['state'] !== $session->oauth2_state)) {

                unset($session->oauth2_state);
                throw new \RuntimeException('Invalide State.');

            } else {

                try {

                    // Try to get an access token using the authorization code grant.
                    $accessToken = $provider->getAccessToken('authorization_code', [
                        'code' => $_GET['code']
                    ]);

                    // Using the access token, we may look up details about the
                    // resource owner.
                    $resourceOwner = $provider->getResourceOwner($accessToken);

                    $user_data = $resourceOwner->toArray();
                                       
                    $user = User::load($user_data['uid']);

                    $user->set('email', $user_data['email']);
                    $user->set('fullname', $user_data['name']);
                    $user->set('state', 'enabled');
                    $user->set('language', 'en');
                    $user->set('access',    [
                                                'site' => 
                                                    [
                                                        'login' => 'true'
                                                    ],
                                                'admin' => 
                                                    [
                                                        'super' => 'true', 
                                                        'login' => 'true'
                                                    ]
                    ]);

                    // @TODO : Check if necessary
                    $user->authenticated = true;
                    $user->authorized = true;

                    // Save user
                    $user->save();
                    
                    // Push user on System
                    $this->grav['user']->merge($user->toArray());

                } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

                    // Failed to get the access token or user details.
                    throw new \RuntimeException($e->getMessage());

                }
            }
        }
    }
}
