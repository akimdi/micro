<?php /** MicroSession */

namespace Micro\web;

/**
 * Session is a Session manager
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/antivir88/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @version 1.0
 * @since 1.0
 * @property
 */
class Session
{
    /**
     * Construct for this class
     *
     * @access public
     * @param array $config configuration array
     * @result void
     */
    public function __construct(array $config = [])
    {
        if (isset($config['autoStart']) AND ($config['autoStart'] == true)) {
            $this->create();
        }
    }

    /**
     * Create a new session or load prev session
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /**
     * Destroy session
     *
     * @access public
     * @return void
     */
    public function destroy()
    {
        if (isset($_SESSION)) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Getter session element
     *
     * @access public
     * @param string $name element name
     * @return mixed
     */
    public function __get($name)
    {
        return (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
    }

    /**
     * Setter session element
     *
     * @access public
     * @param string $name element name
     * @param mixed $value element value
     * @return void
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Is set session element
     *
     * @access public
     * @param string $name element name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Unset session element
     *
     * @access public
     * @param string $name element name
     * @return void
     */
    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }
}