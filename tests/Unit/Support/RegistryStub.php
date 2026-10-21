<?php

/**
 * Kunena Component
 *
 * A very small stand-in for Joomla\Registry\Registry (the parameters of a category), only for the unit tests which run without
 * Joomla: it reads values from an array or a json text.
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Registry;

if (!class_exists(Registry::class)) {
    class Registry
    {
        private array $data;

        public function __construct($data = null)
        {
            $this->data = \is_string($data) ? (array) json_decode($data, true) : (array) $data;
        }

        public function get($path, $default = null)
        {
            return $this->data[$path] ?? $default;
        }

        public function set($path, $value)
        {
            $this->data[$path] = $value;

            return $value;
        }
    }
}
