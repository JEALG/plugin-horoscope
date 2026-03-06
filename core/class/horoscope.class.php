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
            log::add('horoscope', 'debug', '│ ' . __('Création Commande', __FILE__) . ' : ' . $Name . ' ── ' . __('Type / SubType', __FILE__) . ' : '  . $Type . '/' . $SubType . ' ── LogicalID : ' . $_logicalId . ' ── Template Widget / Ligne : ' . $Template . '/' . $forceLineB . ' ── ' . __('Type de générique', __FILE__) . ' : ' . $generic_type . ' ── ' . __('Icône', __FILE__) . ' : ' . $icon .   ' ── ' . __('Ordre', __FILE__) . $_order);
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

    public static function getHoroscopeForSigne($signe_zodiaque, $type_horsocope, $name)
    {
        if ($type_horsocope === 'astro_jour') {
            $url = "https://raw.githubusercontent.com/kayoo123/astroo-api/main/docs/jour.json";
        } else {
            $url = "https://raw.githubusercontent.com/kayoo123/astroo-api/main/docs/hebdomadaire.json";
        }

        $jsonStr = file_get_contents($url);
        $data = json_decode($jsonStr, true);
        $data_log = str_replace(["\r", "\n"], "", $jsonStr);
        $horoscope['signe'] = $signe_zodiaque;
        log::add('horoscope', 'debug', '│┌── :fg-info:' . __('Info requête', __FILE__) . ':/fg: ──');
        log::add('horoscope', 'debug', '│| :fg-info:URL : :/fg:' . $url);
        if (!is_array($data)) {
            log::add('horoscope', 'debug', '││:fg-danger:' . __('Le fichier Json est vide', __FILE__) . ' ───▶︎ ' .  __('Pas de mise à jour', __FILE__) . ':/fg:');
            return false;
        } else {
            log::add('horoscope', 'debug', '|| :fg-info:' . __('Valeur Json', __FILE__) . ':/fg: ' . str_replace(["\r", "\n"], "", $jsonStr));
        }


        try {
            foreach ($data as $nomSigne => $description) {
                if ($nomSigne == 'date') {
                    $horoscope['date'] = trim($description);
                }
                if ($nomSigne === $signe_zodiaque) {
                    $horoscope['horoscope'] = trim($description);
                }
            }
            log::add('horoscope', 'debug', '││ :fg-info:' . __('Valeur de la date', __FILE__) . ' ::/fg: ' . $horoscope['date']);
            log::add('horoscope', 'debug', '││ :fg-info:' . __('Valeur de l\'horoscope', __FILE__) . ' ::/fg: ' . $horoscope['horoscope']);
        } catch (Exception $exc) {
            log::add('horoscope', 'error', __('Erreur pour la récupération des données sur le site internet pour', __FILE__) . ' ' . $name . ' : ' . $exc->getMessage());
        }

        log::add('horoscope', 'debug', '│└─────────');


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
            $this->setConfiguration('type_horoscope', 'traditionnel');
        }
    }

    public function postInsert() {}

    public function preSave() {}

    public function postSave()
    {
        $Equipement = eqlogic::byId($this->getId());
        //log::add('horoscope', 'debug', 'postSave() => ' . $_eqName);
        if ($this->getConfiguration('type_horoscope') == '') {
            $this->setConfiguration('type_horoscope', 'traditionnel');
        }

        /*  ********************** Creéation des commandes signe *************************** */
        log::add('horoscope', 'debug', '┌── :fg-success:' . __('Création de la commande si besoin pour', __FILE__) . ' : '  . $this->getName() . ':/fg: ──');
        $horo_type = $this->getConfiguration('type_horoscope');
        $horo_signe = $this->getConfiguration('signe');
        $horo_Template = 'horoscope::Signe zodiaque';
        $order = 1;
        $Equipement->AddCommand((__('Signe du zodiaque', __FILE__)), 'signe', 'info', 'string', $horo_Template, null, 1, 'default', 'default',  $order, null, null, null);
        $order++;
        $Equipement->AddCommand((__('Date de l\'Horoscope', __FILE__)), 'date', 'info', 'string', 'GENERIC_INFO', null, '0', 'default', 1,  $order, null, null, null);
        $order++;
        $Equipement->AddCommand((__('Horoscope', __FILE__)), 'horoscope', 'info', 'string', 'GENERIC_INFO', null, 1, 'default', 1,  $order, null, null, 'core:line');
        /*  ********************** Creéation des commandes suivant Horoscope *************************** */
        $order++;
        //log::add('horoscope', 'debug', '| ───▶︎ ' . __('Type Horoscope', __FILE__) . ' : ' . $horo_type);
        log::add('horoscope', 'debug', '| ───▶︎ ' . __('Signe', __FILE__) . ' : '  .  $horo_signe);
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

        log::add('horoscope', 'debug', '┌── :fg-success:' . __('Mise à jour', __FILE__) . ' ::/fg: '  . $this->getName() . ' (' . $this->getHumanName() . ') ──');

        /*  ********************** Récupération signe *************************** */
        log::add('horoscope', 'debug', '│┌── :fg-success:' . __('Configuration de l\'équipement', __FILE__) . ' ::/fg: '   . $this->getName() . ' ──');
        $signe_zodiaque = $this->getConfiguration('signe');
        if ($signe_zodiaque == '') {
            log::add('horoscope', 'error', '││ ───▶︎' . __('Configuration : Signe zodiaque inexistant', __FILE__) . ' : ' . $this->getConfiguration('signe_zodiaque'));
            throw new Exception(__('Le champ SIGNE DU ZODIAQUE ne peut être vide', __FILE__));
        }
        log::add('horoscope', 'debug', '││ ───▶︎ :fg-info:' . __('Signe du zodiaque', __FILE__) . ' :/fg:: ' . $signe_zodiaque);

        /*  ********************** Du type d'horoscope signe *************************** */
        if ($this->getConfiguration('type_horoscope') == '') {
            $this->setConfiguration('type_horoscope', 'traditionnel');
        }
        $type_horsocope = $this->getConfiguration('type_horoscope');

        log::add('horoscope', 'debug', '││ ───▶︎ :fg-info:' . __('Type d\'horosocope', __FILE__) . ' :/fg:: ' . $type_horsocope);
        log::add('horoscope', 'debug', '│└─────────');

        $horoscope = self::getHoroscopeForSigne($signe_zodiaque, $type_horsocope, $this->getName());
        if ($horoscope != false) {
            log::add('horoscope', 'debug', '│┌── :fg-info:' . __('Mise à jour de l\'équipement', __FILE__) . ' ::/fg: ' . $this->getName() . ' ──');
            foreach ($horoscope as $name => $message) {
                if (!is_string($message)) {
                    continue;
                }
                log::add('horoscope', 'debug', "││:fg-info: ───▶︎ {$name} ::/fg: {$message}");
                //if (is_object($theme_name)) {
                $this->checkAndUpdateCmd($name, $message);
                //}
            }
        }
        log::add('horoscope', 'debug', '│└─────────');
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
