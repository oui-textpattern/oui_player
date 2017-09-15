<?php

/*
 * This file is part of oui_player,
 * an extendable plugin to easily embed
 * customizable players in Textpattern CMS.
 *
 * https://github.com/NicolasGraph/oui_player
 *
 * Copyright (C) 2016-2017 Nicolas Morand
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT
 * OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * Youtube
 *
 * @package Oui\Player
 */

namespace Oui\Player {

    class Youtube extends Provider
    {
        protected static $patterns = array(
            'video' => array(
                'scheme' => '#^(http|https)://(www\.)?(youtube\.com/(watch\?v=|embed/|v/)|youtu\.be/)(([^&?/]+)?)#i',
                'id'     => '5',
                'glue'   => '&amp;',
            ),
            'list'  => array(
                'scheme' => '#^(http|https)://(www\.)?(youtube\.com/(watch\?v=|embed/|v/)|youtu\.be/)[\S]+list=([^&?/]+)?#i',
                'id'     => '5',
                'prefix' => 'list=',
            ),
        );
        protected static $src = '//www.youtube-nocookie.com/';
        protected static $glue = array('embed/', '?', '&amp;');
        protected static $params = array(
            'autohide'       => array(
                'default' => '2',
                'valid'   => array('0', '1', '2'),
            ),
            'autoplay'       => array(
                'default' => '0',
                'valid'   => array('0', '1'),
            ),
            'cc_load_policy' => array(
                'default' => '1',
                'valid'   => array('0', '1'),
            ),
            'color'          => array(
                'default' => 'red',
                'valid'   => array('red', 'white'),
            ),
            'controls'       => array(
                'default' => '1',
                'valid'   => array('0', '1', '2'),
            ),
            'disablekb'      => array(
                'default' => '0',
                'valid'   => array('0', '1'),
            ),
            'enablejsapi'    => array(
                'default' => '0',
                'valid'   => array('0', '1'),
            ),
            'end'            => array(
                'default' => '',
                'valid'   => 'number',
            ),
            'fs'             => array(
                'default' => '1',
                'valid'   => array('0', '1'),
            ),
            'hl'             => array(
                'default' => '',
            ),
            'iv_load_policy' => array(
                'default' => '1',
                'valid'   => array('1', '3'),
            ),
            'list'           => array(
                'default' => '',
            ),
            'listType'           => array(
                'default' => '',
                'valid'   => array('playlist', 'search', 'user_uploads'),
            ),
            'loop'           => array(
                'default' => '0',
                'valid'   => array('0', '1'),
            ),
            'modestbranding' => array(
                'default' => '0',
                'valid'   => array('0', '1'),
            ),
            'origin'    => array(
                'default' => '',
                'valid'   => 'url',
            ),
            'playlist'    => array(
                'default' => '',
            ),
            'playsinline'    => array(
                'default' => '0',
                'valid'   => array('0', '1'),
            ),
            'rel'            => array(
                'default' => '1',
                'valid'   => array('0', '1'),
            ),
            'start'          => array(
                'default' => '0',
                'valid'   => 'number',
            ),
            'showinfo'       => array(
                'default' => '1',
                'valid'   => array('0', '1'),
            ),
            'theme'          => array(
                'default' => 'dark',
                'valid'   => array('dark', 'light'),
            ),
        );

        /**
         * {@inheritdoc}
         */

        public function getInfos()
        {
            $isUrl = preg_grep('/([.][a-z]+\/)/', $this->getPlay());

            if ($this->infos || ($isUrl && $this->setInfos() !== false)) {
                $infos = $this->infos;
            } else {
                $infos = array();

                foreach ($this->getPlay() as $play) {
                    $infos[$play] = array(
                        'play' => $play,
                        'type' => preg_match('#^(list)=#', $play) ? 'list' : 'video',
                    );
                }
            }

            static::$glue[0] = $infos[$this->getPlay()[0]]['type'] === 'list' ? 'embed?' : 'embed/';

            return $infos;
        }
    }

    if (txpinterface === 'admin') {
        Youtube::getInstance();
    }
}
