<?php

//use SimpleXMLElement;

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class horoscope extends eqLogic
{

    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
    public static function deadCmd()
    {
        $return = array();
        foreach (eqLogic::byType('horoscope') as $horoscope) {
            foreach ($horoscope->getCmd() as $cmd) {
                preg_match_all("/#([0-9]*)#/", $cmd->getConfiguration('infoName', ''), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))) {
                        $return[] = array('detail' => __('Horoscope', __FILE__) . ' ' . $horoscope->getHumanName() . ' ' . __('dans la commande', __FILE__) . ' ' . $cmd->getName(), 'help' => __('Nom Information', __FILE__), 'who' => '#' . $cmd_id . '#');
                    }
                }
                preg_match_all("/#([0-9]*)#/", $cmd->getConfiguration('calcul', ''), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))) {
                        $return[] = array('detail' => __('Horosocope', __FILE__) . ' ' . $horoscope->getHumanName() . ' ' . __('dans la commande', __FILE__) . ' ' . $cmd->getName(), 'help' => __('Calcul', __FILE__), 'who' => '#' . $cmd_id . '#');
                    }
                }
            }
        }
        return $return;
    }
    //Fonction Widget
    public static $_widgetPossibility = array('custom' => true);

    public function AddCommand($Name, $_logicalId, $Type = 'info', $SubType = 'binary', $Template = null, $generic_type = null, $IsVisible = 1, $icon = 'default', $forceLineB = 'default',  $_order = null, $_iconname = null, $_noiconname = null, $_generic_type = 'GENERIC_INFO')
    {
        $Cmd = $this->getCmd(null, $_logicalId);
        if ($SubType === 'numeric') {
            $Template  = 'core::' . 'line';
        }
        if (!is_object($Cmd)) {
            log::add('horoscope', 'debug', '│' . __('Création Commande', __FILE__) . ' : ' . $Name . ' ── ' . __('Type / SubType', __FILE__) . ' : '  . $Type . '/' . $SubType . ' ── LogicalID : ' . $_logicalId . ' ── Template Widget / Ligne : ' . $Template . '/' . $forceLineB . ' ── ' . __('Type de générique', __FILE__) . ' : ' . $generic_type . ' ── ' . __('Icône', __FILE__) . ' : ' . $icon .   ' ── ' . __('Ordre', __FILE__) . $_order);
            $Cmd = new horoscopeCmd();
            $Cmd->setId(null);
            $Cmd->setLogicalId($_logicalId);
            $Cmd->setEqLogic_id($this->getId());
            $Cmd->setName($Name);

            $Cmd->setType($Type);
            $Cmd->setSubType($SubType);

            if ($Template != null) {
                $Cmd->setTemplate('dashboard', $Template);
                $Cmd->setTemplate('mobile', $Template);
            }
            if ($SubType === 'numeric') {
                $Cmd->setDisplay('forceReturnLineBefore', 1);
                $Cmd->setDisplay('forceReturnLineAfter', 1);
            }
            $Cmd->setIsVisible($IsVisible);

            if ($icon != 'default') {
                $Cmd->setDisplay('icon', '<i class="' . $icon . '"></i>');
            }
            if ($forceLineB != 'default') {
                $Cmd->setDisplay('forceReturnLineBefore', 1);
            }
            if ($_iconname != 'default') {
                $Cmd->setDisplay('showIconAndNamedashboard', 1);
            }
            if ($_noiconname != null) {
                $Cmd->setDisplay('showNameOndashboard', 0);
            }
            if ($_generic_type != null) {
                $Cmd->setDisplay('generic_type', 'GENERIC_INFO');
            }
            if ($generic_type != null) {
                $Cmd->setGeneric_type($generic_type);
            }
            if ($_logicalId == 'signe') {
                $Cmd->setConfiguration('data', 'signe');
            }
            if ($_order != null) {
                $Cmd->setOrder($_order);
            }
            $Cmd->save();
        }

        /*     * ********************* Commande REFRESH ************************* */
        $createRefreshCmd = true;
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = cmd::byEqLogicIdCmdName($this->getId(), __('Rafraichir', __FILE__));
            if (is_object($refresh)) {
                $createRefreshCmd = false;
            }
        }
        if ($createRefreshCmd) {
            if (!is_object($refresh)) {
                $refresh = new horoscopeCmd();
                $refresh->setLogicalId('refresh');
                $refresh->setIsVisible(1);
                $refresh->setName(__('Rafraichir', __FILE__));
            }
            $refresh->setType('action');
            $refresh->setSubType('other');
            $refresh->setEqLogic_id($this->getId());
            $refresh->save();
        }
        return $Cmd;
    }

    public static function templateWidget()
    {
        $return = array('info' => array('string' => array()));
        $return['info']['string']['Signe zodiaque'] = array(
            'template' => 'tmplmultistate',
            'replace' => array('#_desktop_width_#' => '60'),
            'test' => array(
                array('operation' => "#value# == 'balance'", 'state_light' => '<img src=plugins/horoscope/core/template/img/balance.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/balance_dark.png>'),
                array('operation' => "#value# == 'belier'", 'state_light' => '<img src=plugins/horoscope/core/template/img/belier.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/belier_dark.png>'),
                array('operation' => "#value# == 'cancer'", 'state_light' => '<img src=plugins/horoscope/core/template/img/cancer.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/cancer_dark.png>'),
                array('operation' => "#value# == 'capricorne'", 'state_light' => '<img src=plugins/horoscope/core/template/img/capricorne.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/capricorne_dark.png>'),
                array('operation' => "#value# == 'gemeaux'", 'state_light' => '<img src=plugins/horoscope/core/template/img/gemeaux.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/gemeaux_dark.png>'),
                array('operation' => "#value# == 'lion'", 'state_light' => '<img src=plugins/horoscope/core/template/img/lion.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/lion_dark.png>'),
                array('operation' => "#value# == 'poissons'", 'state_light' => '<img src=plugins/horoscope/core/template/img/poissons.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/poissons_dark.png>'),
                array('operation' => "#value# == 'sagittaire'", 'state_light' => '<img src=plugins/horoscope/core/template/img/sagittaire.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/sagittaire_dark.png>'),
                array('operation' => "#value# == 'scorpion'", 'state_light' => '<img src=plugins/horoscope/core/template/img/scorpion.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/scorpion_dark.png>'),
                array('operation' => "#value# == 'taureau'", 'state_light' => '<img src=plugins/horoscope/core/template/img/taureau.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/taureau_dark.png>'),
                array('operation' => "#value# == 'vierge'", 'state_light' => '<img src=plugins/horoscope/core/template/img/vierge.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/vierge_dark.png>'),
                array('operation' => "#value# == 'verseau'", 'state_light' => '<img src=plugins/horoscope/core/template/img/verseau.png>', 'state_dark' => '<img src=plugins/horoscope/core/template/img/verseau_dark.png>')
            )
        );
        return $return;
    }
    public static function ConnexionHoroscope($para_horoscope)
    {
        $variable_url = "https://www.";
        if (config::byKey('horoscope_use_http', 'horoscope') === '1') {
            $variable_url = "http://www.";
        }

        $url = $variable_url . 'astroo.com/horoscopes/' . $para_horoscope['URL'] . '.php';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_USERAGENT =>
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ' .
                'AppleWebKit/537.36 (KHTML, like Gecko) ' .
                'Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cache-Control: no-cache',
                'Pragma: no-cache'
            ]
        ]);
        if ($para_horoscope['horo_type'] == 'day') {
            $type_log = __('Info requête directe pour l\'horoscope du jour', __FILE__);
        } else {
            $type_log = __('Info requête directe pour l\'horoscope hebdomadaire', __FILE__);
        }
        log::add('horoscope', 'debug', '│┌───────── :fg-success:' . $type_log . ':/fg: ──');

        $html = curl_exec($ch);
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // PHP < 8.5 : fermeture explicite du handle cURL
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }
        log::add('horoscope', 'debug', '││ :fg-info:' . __('URL', __FILE__) . ':/fg: : ' . $url . ' :fg-info:── ' . __('Code HTTP', __FILE__) . ':/fg: : ' . $http_code);
        // Traitement des différentes erreurs possible 
        // Erreur cURL
        $error_site = false;
        $message_erreur = '';
        if ($html === false) {
            $error_site = true;
            $message_erreur = __('Erreur cURL', __FILE__) . $curl_errno . ' - ' . $curl_error;
        }

        // Redirection
        if ($http_code >= 300 && $http_code < 400) {
            $error_site = true;
            $message_erreur = __('Redirection détectée - Code HTTP', __FILE__) . ' ' . $curl_errno . ' : ' . $http_code;
        }

        // Code HTTP différent de 200
        if ($http_code != 200) {
            $error_site = true;
            $message_erreur = __('Erreur HTTP - Code HTTP', __FILE__) . ' ' . $curl_errno . ' : ' . $http_code;
        }
        if ($error_site == true) {
            log::add('horoscope', 'error', $message_erreur);
            log::add('horoscope', 'debug', '│└─────────');
        } else {
            log::add('horoscope', 'debug', '││ ' . 'OK ' . ':fg-info:' . __('Connexion avec', __FILE__) . ' Astroo :/fg:');
        }
        $para_horoscope_return = array(
            'error_site' => $error_site,
            'html' => $html
        );
        return $para_horoscope_return;
    }

    public static function getHoroscopeForSigne_Day($signe_zodiaque, $name, $horo_type)
    {
        $horoscope['signe'] = $signe_zodiaque;
        $PARA_Connexion_horoscope = array(
            'signe' => $signe_zodiaque,
            'horo_type' => 'day',
            'URL' => 'horoscope_' . strtolower($signe_zodiaque),
        );

        if ($horo_type == 'astro_jour' || $horo_type == 'astro_jour_hebdo') {
            $para_horoscope_return = self::ConnexionHoroscope($PARA_Connexion_horoscope);
            if ($para_horoscope_return['error_site'] == false) {
                try {
                    $html = html_entity_decode($para_horoscope_return['html'], ENT_QUOTES, 'UTF-8');
                    preg_match('/class="tegb"\s+style="font:18px[^>]*>(.*?)<\/p>/si', $html, $matches);
                    $horoscope['date'] = date('d-m-Y');

                    if (isset($matches[1])) {
                        $texte = trim(strip_tags($matches[1]));
                        $texte_nettoye = preg_replace('/Cette semaine\s*\*?\.\.\.\s*$/i', '', $texte);
                        $horoscope['horoscope'] = trim($texte_nettoye);
                        log::add('horoscope', 'debug', '││ ' . 'OK ' . ':fg-info:' . __('Texte récupéré avec succès', __FILE__) . ':/fg:');
                    } else {
                        log::add('horoscope', 'error', __('Structure HTML modifiée ou signe introuvable', __FILE__) . $signe_zodiaque);
                        $horoscope['horoscope'] = __('Données indisponibles', __FILE__);
                    }
                } catch (Exception $exc) {
                    log::add('horoscope', 'error', __('Erreur pour la récupération des données pour l\'horoscope du jour sur le site internet pour', __FILE__) . ' ' . $name . ' : ' . $exc->getMessage());
                }

                log::add('horoscope', 'debug', '│└─────────');
            }
        }

        if ($horo_type == 'astro_hebdo' || $horo_type == 'astro_jour_hebdo') {
            $horoscope = self::getHoroscopeForSigne_Hebdo($signe_zodiaque, $name, $horoscope);
        }

        return $horoscope;
    }


    public static function getHoroscopeForSigne_hebdo($signe_zodiaque, $name, $horoscope)
    {
        $horoscope['signe'] = $signe_zodiaque;
        $PARA_Connexion_horoscope = array(
            'signe' => $signe_zodiaque,
            'horo_type' => 'weekly',
            'URL' => 'horoscope_hebdo_' . strtolower($signe_zodiaque),
        );

        $para_horoscope_return = self::ConnexionHoroscope($PARA_Connexion_horoscope);
        if ($para_horoscope_return['error_site'] == false) {
            try {
                $html = html_entity_decode($para_horoscope_return['html'], ENT_QUOTES, 'UTF-8');
                $horoscope['date_hebdo'] = date('d-m-Y');

                preg_match_all('/<td class="hhte" valign="top">(.*?)<\/td>/si', $html, $matches);

                $decans = [];
                if (isset($matches[1]) && count($matches[1]) >= 3) {
                    for ($i = 0; $i < 3; $i++) {
                        $texte = trim(strip_tags($matches[1][$i]));
                        // Remplace les espaces multiples, retours à la ligne et tabulations
                        $texte = preg_replace('/\s+/', ' ', $texte);
                        // Supprime "Cette semaine..." à la fin du texte 
                        $texte = preg_replace('/Cette semaine\s*\.\.\.\s*$/i', '', $texte);
                        $decans[] = trim($texte);
                    }

                    $horoscope['1_DECAN'] = $decans[0];
                    $horoscope['2_DECAN'] = $decans[1];
                    $horoscope['3_DECAN'] = $decans[2];
                    log::add('horoscope', 'debug', '││ ' . 'OK ' . ':fg-info:' . __('Texte récupéré avec succès', __FILE__) . ':/fg:');
                } else {
                    log::add('horoscope', 'error', '││ :fg-danger:' . __('Impossible de trouver les 3 blocs de décans dans le HTML', __FILE__) . ':/fg:');
                    $horoscope['1_DECAN'] = __('Données indisponibles', __FILE__);
                    $horoscope['2_DECAN'] = __('Données indisponibles', __FILE__);
                    $horoscope['3_DECAN'] = __('Données indisponibles', __FILE__);
                }
            } catch (Exception $exc) {
                log::add('horoscope', 'error', __('Erreur pour la récupération des données pour l\'horoscope hebdomadaire sur le site internet pour', __FILE__) . ' ' . $name . ' : ' . $exc->getMessage());
            }

            log::add('horoscope', 'debug', '│└─────────');
        }
        return $horoscope;
    }

    //Fonction exécutée automatiquement
    public static function cron()
    {
        foreach (eqLogic::byType('horoscope', true) as $eqLogic) {
            $autorefresh = $eqLogic->getConfiguration('autorefresh');
            if ($autorefresh != '') {
                try {
                    $cron = new Cron\CronExpression(checkAndFixCron($autorefresh), new Cron\FieldFactory);
                    if ($cron->isDue()) {
                        try {
                            //log::add('horoscope', 'debug', __('Mise à jour des valeurs pour', __FILE__) . ' : ' . $eqLogic->getName());
                            $eqLogic->getinformations();
                        } catch (Exception $exc) {
                            log::add('horoscope', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getName() . ' : ' . $exc->getMessage());
                        }
                    }
                } catch (Exception $exc) {
                    log::add('horoscope', 'error', __('Expression cron non valide pour', __FILE__) . ' ' . $eqLogic->getName() . ' : ' . $autorefresh);
                }
            }
        }
    }

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert()
    {
        if ($this->getConfiguration('autorefresh') == '') {
            $this->setConfiguration('autorefresh', '0 5 * * *');
        }
        if ($this->getConfiguration('type_horoscope') == '') {
            $this->setConfiguration('type_horoscope', 'astro_jour');
        }
    }

    public function postInsert() {}

    public function preSave() {}

    public function postSave()
    {
        $Equipement = eqlogic::byId($this->getId());
        //log::add('horoscope', 'debug', 'postSave() => ' . $_eqName);
        if ($this->getConfiguration('type_horoscope') == '') {
            $this->setConfiguration('type_horoscope', 'astro_jour');
        }

        /*  ********************** Creéation des commandes signe *************************** */

        $horo_type = $this->getConfiguration('type_horoscope');
        $horo_signe = $this->getConfiguration('signe');
        $horo_Template = 'horoscope::Signe zodiaque';
        $order = 1;
        $Equipement->AddCommand((__('Signe du zodiaque', __FILE__)), 'signe', 'info', 'string', $horo_Template, null, 1, 'default', 'default',  $order, null, null, null);
        $order++;
        if ($horo_type == 'astro_jour' || $horo_type == 'astro_jour_hebdo') {
            $order = 10;
            if (!is_object($Equipement->getCmd(null, 'Date')) || !is_object($Equipement->getCmd(null, 'horoscope'))) {
                log::add('horoscope', 'debug', '┌───────── :fg-info:' . __('Création des commandes si besoin pour l\'horoscope du jour', __FILE__) . ' : '  . $this->getName() . ':/fg: ──');
                $Equipement->AddCommand((__('Date Horoscope - Jour', __FILE__)), 'date', 'info', 'string', 'GENERIC_INFO', null, '0', 'default', 1,  $order++, null, null, null);
                $Equipement->AddCommand((__('Horoscope', __FILE__)), 'horoscope', 'info', 'string', 'GENERIC_INFO', null, 1, 'default', 1,  $order++, null, null, 'core:line');
                log::add('horoscope', 'debug', '└─────────');
            }
        }
        if ($horo_type == 'astro_hebdo' || $horo_type == 'astro_jour_hebdo') {
            $order = 20;
            if (!is_object($Equipement->getCmd(null, 'date_hebdo')) || !is_object($Equipement->getCmd(null, '1_DECAN')) || !is_object($Equipement->getCmd(null, '2_DECAN')) || !is_object($Equipement->getCmd(null, '3_DECAN'))) {
                log::add('horoscope', 'debug', '┌─────────:fg-info:' . __('Création des commandes si besoin pour l\'horoscope hebdomadaire', __FILE__) . ' : '  . $this->getName() . ':/fg: ──');
                $Equipement->AddCommand((__('Date Horoscope - Hebdomadaire', __FILE__)), 'date_hebdo', 'info', 'string', 'GENERIC_INFO', null, '0', 'default', 1,  $order++, null, null, null);
                $Equipement->AddCommand((__('1er Décan', __FILE__)), '1_DECAN', 'info', 'string', 'GENERIC_INFO', null, 1, 'default', 1,  $order++, null, null, 'core:line');
                $Equipement->AddCommand((__('2nd Décan', __FILE__)), '2_DECAN', 'info', 'string', 'GENERIC_INFO', null, 1, 'default', 1,  $order++, null, null, 'core:line');
                $Equipement->AddCommand((__('3eme Décan', __FILE__)), '3_DECAN', 'info', 'string', 'GENERIC_INFO', null, 1, 'default', 1,  $order++, null, null, 'core:line');
                log::add('horoscope', 'debug', '└─────────');
            }
        }
        /*  ********************** Creéation des commandes suivant Horoscope *************************** */
        $order++;
        log::add('horoscope', 'debug', '┌───────── :fg-success:' . __('Rappel du signe', __FILE__) . ' : '  . $this->getName() . ':/fg: ──');
        log::add('horoscope', 'debug', '| ───▶︎ :fg-info:' . __('Signe du zodiaque', __FILE__) . ' :/fg:: '  .  $horo_signe);
        log::add('horoscope', 'debug', '└─────────');

        $this->getInformations();
    }

    public function preUpdate()
    {
        if (!$this->getIsEnable()) return;

        /*  ********************** Récupération signe *************************** */
        $signe_zodiaque = $this->getConfiguration('signe');

        if ($signe_zodiaque == '') {
            log::add('horoscope', 'error', '│ ' . __('Configuration : Signe zodiaque inexistant : ', __FILE__) . $this->getConfiguration('signe'));
            throw new Exception(__('Le champ "Signe du zodiaque" ne peut être vide', __FILE__));
        }
        /*  ********************** Du type d'horoscope signe *************************** */
        if ($this->getConfiguration('type_horoscope') == '' || $this->getConfiguration('type_horoscope') == 'traditionnel') {
            $this->setConfiguration('type_horoscope', 'astro_jour');
        }
    }

    public function postUpdate() {}

    public function preRemove() {}

    public function postRemove() {}

    public function getImage()
    {
        if ($this->getConfiguration('signe') != '') {
            $filename = 'plugins/horoscope/core/config/img/' . $this->getConfiguration('signe') . '.png';
            if (file_exists(__DIR__ . '/../../../../' . $filename)) {
                return $filename;
            }
        }
        return 'plugins/horoscope/plugin_info/horoscope_icon.png';
    }

    /*     * **********************Getteur Setteur*************************** */


    public function getInformations()
    {
        if (!$this->getIsEnable()) return;

        log::add('horoscope', 'debug', '┌───────── :fg-success:' . __('Mise à jour', __FILE__) . ' ::/fg: '  . $this->getName() . ' (' . $this->getHumanName() . ') ──');

        /*  ********************** Récupération signe *************************** */
        log::add('horoscope', 'debug', '│┌───────── :fg-success:' . __('Configuration de l\'équipement', __FILE__) . ' ::/fg: '   . $this->getName() . ' ──');
        $signe_zodiaque = $this->getConfiguration('signe');
        if ($signe_zodiaque == '') {
            log::add('horoscope', 'error', '││ ───▶︎' . __('Configuration : Signe zodiaque inexistant', __FILE__) . ' : ' . $this->getConfiguration('signe_zodiaque'));
            throw new Exception(__('Le champ SIGNE DU ZODIAQUE ne peut être vide', __FILE__));
        }
        log::add('horoscope', 'debug', '││ ───▶︎ :fg-info:' . __('Signe du zodiaque', __FILE__) . ' :/fg:: ' . $signe_zodiaque);

        /*  ********************** Du type d'horoscope signe *************************** */
        if ($this->getConfiguration('type_horoscope') == '') {
            $this->setConfiguration('type_horoscope', 'astro_jour');
        }
        $horo_type = $this->getConfiguration('type_horoscope');

        log::add('horoscope', 'debug', '││ ───▶︎ :fg-info:' . __('Source et type d\'horosocope', __FILE__) . ' :/fg:: ' . $horo_type);
        log::add('horoscope', 'debug', '│└─────────');
        $horoscope = self::getHoroscopeForSigne_Day($signe_zodiaque, $this->getName(), $horo_type);
        if ($horoscope != false) {
            log::add('horoscope', 'debug', '│┌───────── :fg-success:' . __('Mise à jour de l\'équipement pour l\'horoscope du jour', __FILE__) . ' ::/fg: ' . $this->getName() . ' ──');
            foreach ($horoscope as $name => $message) {
                if (!is_string($message)) {
                    continue;
                }
                log::add('horoscope', 'debug', "││:fg-info: ───▶︎ {$name} ::/fg: {$message}");
                $this->checkAndUpdateCmd($name, $message);
            }
            log::add('horoscope', 'debug', '│└─────────');
        }
        log::add('horoscope', 'debug', '└─────────');
    }
    /*     * **********************Getteur Setteur*************************** */
}

class horoscopeCmd extends cmd
{
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
    public function dontRemoveCmd()
    {
        if ($this->getLogicalId() == 'refresh') {
            return true;
        }
        return false;
    }
    public function execute($_options = array())
    {
        if ($this->getLogicalId() == 'refresh') {
            log::add('horoscope', 'debug', ' ─────────▶︎ ' . (__('Début de l\'actualisation manuelle', __FILE__)));
            $this->getEqLogic()->getInformations();
            log::add('horoscope', 'debug', ' ─────────▶︎ ' . (__("Fin de l\'actualisation manuelle", __FILE__)));
            return;
        } else {
            log::add('horoscope', 'debug', '│  [WARNING] ' . __("Pas d'action pour la commande execute",  __FILE__) . ' : ' . $this->getLogicalId());
        }
    }
}
