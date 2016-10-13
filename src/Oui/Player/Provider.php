<?php

/*
 * oui_player - Easily embed customized players..
 *
 * https://github.com/NicolasGraph/oui_player
 *
 * Copyright (C) 2016 Nicolas Morand
 *
 * This file is part of oui_player.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Oui\Player {

    abstract class Provider
    {
        protected static $instances = array();
        protected $provider = '';
        protected $glue = array('?', '&amp;');

        public static function getInstance()
        {
            $class = get_called_class();
            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class;
            }

            return self::$instances[$class];
        }

        final private function __construct()
        {
        }

        final private function __clone()
        {
        }

        /**
         * Register callbacks.
         */
        public function plugProvider()
        {
            // Plug in Oui\Player class
            $plugin = strtolower(str_replace('\\', '_', __NAMESPACE__));
            register_callback(array($this, 'getProvider'), $plugin, 'plug_providers', 0);
        }

        /**
         * Return the class name as the provider name.
         */
        public function getProvider($event, $step, $rs)
        {
            return array(substr(strrchr(get_class($this), '\\'), 1));
        }

        public function getPrefs($prefs)
        {
            foreach ($this->params as $pref => $options) {
                $options['group'] = strtolower(str_replace('\\', '_', get_class($this)));
                $pref = $options['group'] . '_' . $pref;
                $prefs[$pref] = $options;
            }

            return $prefs;
        }

        /**
         * Get a tag attribute list
         *
         * @param string $tag      The plugin tag
         * @param array  $get_atts The array where attributes are stored provider after provider.
         */
        public function getAtts($tag, $get_atts)
        {
            foreach ($this->params as $att => $options) {
                $att = str_replace('-', '_', $att);
                $get_atts[$att] = $options;
            }

            return $get_atts;
        }

        /**
         * Get the video provider and the video id from its url
         *
         * @param string $play The item url to play
         */
        public function getItemInfos($play)
        {

            foreach ($this->patterns as $pattern => $id) {
                if (preg_match($pattern, $play, $matches)) {
                    $match = array(
                        'provider' => strtolower(substr(strrchr(get_class($this), '\\'), 1)),
                        'id'       => $matches[$id],
                    );

                    return $match;
                }
            }

            return false;
        }

        /**
         * Get the provider player url and its parameters/attributes
         */
        public function getParams()
        {
            $player_infos = array(
                'src'    => $this->src,
                'params' => $this->params,
            );

            return $player_infos;
        }

        /**
         * Build the code to embed.
         *
         * @param string $src         The iframe source.
         * @param array  $used_params The player parameters in use.
         * @param array  $dims        The player dimensions
         */
        public function getOutput($src, $used_params, $dims)
        {
            if (!empty($used_params)) {
                $glue = strpos($src, $this->glue[0]) ? $this->glue[1] : $this->glue[0];
                $src .= $glue . implode('&amp;', $used_params);
            }

            $width = $dims['width'];
            $height = $dims['height'];

            if ((!$width || !$height)) {
                $ratio = !empty($dims['ratio']) ? $dims['ratio'] : '16:9';

                // Work out the aspect ratio.
                preg_match("/(\d+):(\d+)/", $ratio, $matches);
                if ($matches[0] && $matches[1]!=0 && $matches[2]!=0) {
                    $aspect = $matches[1] / $matches[2];
                } else {
                    $aspect = 1.778;
                }

                // Calcuate the new width/height.
                if ($width) {
                    $height = $width / $aspect;
                } elseif ($height) {
                    $width = $height * $aspect;
                }
            }

            $output = '<iframe width="' . $width . '" height="' . $height . '" src="' . $src . '" frameborder="0" allowfullscreen></iframe>';

            return $output;
        }
    }

}
