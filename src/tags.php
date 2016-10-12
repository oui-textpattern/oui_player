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

namespace {

    /*
     * Display a video
     */
    function oui_player($atts, $thing)
    {
        global $thisarticle;

        // Instanciate Oui_Player.
        $main_class = 'Oui\Player';
        $main_instance = $main_class::getInstance();

        /*
         * Set and check Tag attributes
         */

        // Get tag attributes.
        $get_atts = $main_instance->getAtts(__FUNCTION__);

        // Set the array to be used by latts()
        foreach ($get_atts as $att => $options) {
            $get_atts[$att] = '';
        }

        // Set tag attributes.
        extract(lAtts($get_atts, $atts));

        /*
         * Get video infos
         */
        $play ?: $play = strtolower(get_pref('oui_player_custom_field'));
        $play = isset($thisarticle[$play]) ? $thisarticle[$play] : $play;

        // Check class.
        if ($provider) {
            $provider_class = $main_class . '\\' . $provider;
            $provider_instance = $provider_class::getInstance();
            $match = $provider_instance->getItemInfos($play);
        } else {
            $match = $main_instance->getItemInfos($play);
        }

        // Check if the video is recognize as a video url.
        if ($match) {
            $provider = $match['provider'];
            $id = $match['id'];
        } else {
            $provider ?: $provider = get_pref('oui_player_provider');
            $id = $play;
        }

        $provider_class = $main_class . '\\' . $provider;
        $provider_instance = $provider_class::getInstance();

        /*
         * Get player Infos
         */
        $provider_prefs = strtolower(str_replace('\\', '_', $provider_class));
        $player_infos = $provider_instance->getParams();
        $src = $player_infos['src'] . $id;
        $params = $player_infos['params'];

        /*
         * Prepare player parameters for the output
         */

        // Create a list of needed parameters
        $used_params = array();
        $ignore = array(
            'height',
            'ratio',
            'width',
        );

        foreach ($params as $param => $infos) {
            if (!in_array($param, $ignore)) {
                $pref = get_pref('oui_player_' . strtolower($provider) . '_' . $param);
                $default = $infos['default'];
                $att_name = str_replace('-', '_', $param);
                $att = $$att_name;

                // Add modified attributes or prefs values as player parameters.
                if ($att === '' && $pref !== $default) {
                    // Remove # from the color pref as a color type is used for the pref input.
                    $used_params[] = $param . '=' . str_replace('#', '', $pref);
                } elseif ($att !== '') {
                    // Remove the # in the color attribute just in case…
                    $used_params[] = $param . '=' . str_replace('#', '', $att);
                }
            }
        }

        /*
         * Get the player size for the output
         */

        // Set an array to be used to get the player size.
        $dims = array(
            'width'  => $width ? $width : get_pref($provider_prefs . '_width'),
            'height' => $height ? $height : get_pref($provider_prefs . '_height'),
            'ratio'  => $ratio ? $ratio : get_pref($provider_prefs . '_ratio'),
        );

        // Check if some player parameters has been used.
        $output = $provider_instance->getOutput($src, $used_params, $dims);

        return doLabel($label, $labeltag).(($wraptag) ? doTag($output, $wraptag, $class) : $output);
    }

    /*
     * Check a video url and its provider if provided.
     */
    function oui_if_player($atts, $thing)
    {
        global $thisarticle;

        // Instanciate Oui_Player.
        $main_class = 'Oui\Player';
        $main_instance = $main_class::getInstance();

        /*
         * Set and check Tag attributes
         */

        // Get tag attributes.
        $get_atts = $main_instance->getAtts(__FUNCTION__);

        // Set the array to be used by latts()
        foreach ($get_atts as $att => $options) {
            $get_atts[$att] = $options['default'];
        }

        // Set tag attributes.
        extract(lAtts($get_atts, $atts));

        /*
         * Get video infos
         */

        $play ?: $play = strtolower(get_pref('oui_player_custom_field'));
        $play = isset($thisarticle[$play]) ? $thisarticle[$play] : $play;

        // Check if the video is recognize as a video url.
        if ($provider) {
            $provider_class = $main_class . '\\' . $provider;
            $provider_instance = $provider_class::getInstance();
            $match = $provider_instance->getItemInfos($play);
        } else {
            $match = $main_instance->getItemInfos($play);
        }

        return parse($thing, $match);
    }
}
