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

    protected static $_theme_mapping = [
        //clin_d_oeil' => 'horoscopeDuJour'
    ];

    /* Le gabarit de l'URL de récupération de l'horoscope - La chaine '%s' sera remplacée par la clé du signe de l'equipement */

    public static $_url_template = 'http://www.asiaflash.com/horoscope/%s.xml';

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

    /* Recuperer l'horoscope du jour et met à jour les commandes */
    public function getHoroscopeName($theme_name)
    {
        switch ($theme_name) {
            case 'Amour':
                $theme_name_cmd =  (__('Amour', __FILE__));
                break;
            case 'Argent':
                $theme_name_cmd =  (__('Argent', __FILE__));
                break;
            case 'signe':
                $theme_name_cmd =  (__('signe', __FILE__));
                break;
            case 'Santé':
                $theme_name_cmd =  (__('Santé', __FILE__));
                break;
            case "Travail":
                $theme_name_cmd =  (__('Travail', __FILE__));
                break;
            case "Famille":
                $theme_name_cmd =  (__('Famille', __FILE__));
                break;
            case "Viesociale":
                $theme_name_cmd =  (__('Vie Sociale', __FILE__));
                break;
            case "Citationdujour":
                $theme_name_cmd =  (__('Citation du jour', __FILE__));
                break;
            case "Nombredechance":
                $theme_name_cmd =  (__('Nombre de chance', __FILE__));
                break;
            case "Clindoeil":
                $theme_name_cmd =  (__('Clin d oeil', __FILE__));
                break;
            default:
                $theme_name_cmd = $theme_name;
        }
        return $theme_name_cmd;
    }
    public function AddCommand($Name, $_logicalId, $Type = 'info', $SubType = 'binary', $Template = null, $generic_type = null, $IsVisible = 1, $icon = 'default', $forceLineB = 'default',  $_order = null, $_iconname = null, $_noiconname = null, $_generic_type = 'GENERIC_INFO', $Equipement = null)
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
    public function AddCommand_theme($horo_signe, $order, $horo_type, $Equipement)
    {
        $horoscope = self::getHoroscopeForSigne($horo_signe, $horo_type, $this->getName());

        // met a jour toutes les commandes contenants les phrases de l'horoscope
        foreach ($horoscope['themes'] as $horo_Name => $message) {
            if (!is_string($message)) {
                continue;
            }
            // Récupération de la traduction de la commande
            $horo_Name_Trad = horoscope::getHoroscopeName($horo_Name);

            // Vérification s'il faut créer la commande
            $create_cmd = horoscope::getHoroscopeCreateCMD($horo_Name, $horo_type);

            // Création des commandes
            if ($create_cmd === true) {
                //log::add('horoscope', 'debug', "│ Création Commande : {$horo_Name}");
                // Récupération du sous type de commande pour Nombre de chance
                if ($horo_Name == 'Nombredechance') {
                    $SubType = 'numeric';
                    // log::add('horoscope', 'debug', "│ Création Commande ==> TYPE  : " . $SubType);
                } else {
                    $SubType = 'string';
                }
                // Création de la commande
                $horo_Template = 'GENERIC_INFO';
                $Equipement->AddCommand($horo_Name_Trad, $horo_Name, 'info', $SubType, $horo_Template, null, 1, 'default', 'default',  $order, null, null, null, $Equipement);
                $order++;
            } else {
                log::add('horoscope', 'debug', "│ " . __('Création Commande', __FILE__) . ' : ' .  "{$horo_Name}" . ' ==> ' . __('Pas de création de la commande / Mise à jour', __FILE__));
            }
        }
        // Mise à jour les commandes specifique declarée dans le tableau de mapping

        foreach ($horoscope['themes_simple'] as $horo_Name => $message) {
            // si un mapping specifique est defini alors on l'applique
            if (isset(self::$_theme_mapping[$horo_Name])) {
                $specific_commande_name = self::$_theme_mapping[$horo_Name];

                // Récupération de la traduction de la commande
                $horo_ID = horoscope::getHoroscopeName($horo_Name);

                // Vérification s'il faut créer la commande
                $create_cmd = horoscope::getHoroscopeCreateCMD($horo_Name, $horo_type);

                if ($create_cmd === true) {
                    // log::add('horoscope', 'debug', "│ Création Commande : {$$horo_Name}");
                    // Récupération du sous type de commande pour Nombre de chance
                    if ($horo_Name == 'Nombredechance') {
                        $SubType = 'numeric';
                        //log::add('horoscope', 'debug', "│ TYPE pour cette Commande : " . $SubType);
                    } else {
                        $SubType = 'string';
                    }
                    // Création de la commande
                    $horo_Template = 'GENERIC_INFO';
                    $Equipement->AddCommand($horo_Name, $horo_ID, 'info', $SubType, 'default', null, 1, 'default', 'default',  $order, null, null, null, $Equipement);
                    $order++;
                }
            }
        }

        return $order;
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

        if ($type_horsocope == 'traditionnel' || $type_horsocope == 'traditionnel_condense') {
            $signe_zodiaque = 'rss_horojour_' . $signe_zodiaque;
        } elseif ($type_horsocope == 'traditionnel_hebdomadaire') {
            $signe_zodiaque = 'rss_hebdotay_complet_' . $signe_zodiaque;
        }

        $url = sprintf(self::$_url_template, $signe_zodiaque);
        log::add('horoscope', 'debug', '│┌── :fg-info:' . __('Info requête', __FILE__) . ':/fg: ──');
        log::add('horoscope', 'debug', '││ :fg-info:URL : :/fg:' . $url);
        try {
            //log::add('horoscope', 'debug', __('Mise à jour des valeurs pour', __FILE__) . ' : ' . $eqLogic->getName());
            //$xmlData = file_get_contents($url);
            $xmlData = file_get_contents($url, false, stream_context_create(array('socket' => array('bindto' => '0:0'))));
            $xml = new SimpleXMLElement($xmlData);

            // contient tous le champ description
            $description = $xml->channel->item->description;
            $title = $xml->channel->item->title;
            log::add('horoscope', 'debug', '││ :fg-info:' . __('Date', __FILE__) . ' ::/fg: ' . $title);
            log::add('horoscope', 'debug', '││ :fg-info:' . __('Description', __FILE__) . ' ::/fg: ' . $description);

            // extrait les paragraphes de la description
            $paragraphes = preg_split('/<br><br>/', $description);

            // la liste horoscope contient une cle par theme de l'horoscope - chaque nom de theme est repris tel quel depuis le XML - en supplement chaque nom de theme est duppliquer en remplacant tous les caracteres non alphabetique par des underscores
            $horoscope = ['themes' => [], 'themes_simple' => []];

            // filtre les paragraphes pour ne retourner que ceux contenant une phrase d'horoscope
            foreach ($paragraphes as $key => $paragraphe) {
                // elimine les paragraphes qui ne commence par la chaine suivante :
                if (substr($paragraphe, 0, strlen('<b>Horoscope')) !== '<b>Horoscope') {
                    unset($paragraphes[$key]);
                } else {
                    $paragraphe = strip_tags($paragraphe);
                    $matches = [];
                    if (preg_match('/^Horoscope\s*[^ ]+\s*-\s*(.*)\n(.*)/', $paragraphe, $matches) > 0) {
                        if (count($matches) == 3) {
                            $theme = $matches[1];
                            $theme = str_replace(' ', '', $theme);
                            $theme = str_replace('\'', '', $theme);
                            // Elime le point en fin de phrase 
                            //$theme2 = rtrim($theme, '.');
                            //log::add('horoscope', 'debug', ' ─────────> Valeur ==> ' . $theme2);
                            $phrase = $matches[2];
                            // Elime le point en fin de phrase 
                            $phrase = rtrim($phrase, '.');
                            //log::add('horoscope', 'debug', ' ─────────> Valeur ==> ' . $phrase);
                            // Fin Elime le point en fin de phrase 
                            $theme_strip = strtolower(preg_replace('/[^\wéè]/', '_', $theme));
                            $horoscope['themes'][$theme] = $phrase;
                            $horoscope['themes_simple'][$theme_strip] = $phrase;
                        }
                    }
                }
            }
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
        //$horo_ID = $this->getConfiguration('signe');
        $horo_ID = 'signe';
        $horo_Name = (__('signe', __FILE__));
        $horo_type = $this->getConfiguration('type_horoscope');
        $horo_signe = $this->getConfiguration('signe');
        $horo_Template = 'horoscope::Signe zodiaque';
        $order = 1;
        $Equipement->AddCommand($horo_Name, $horo_ID, 'info', 'string', $horo_Template, null, 1, 'default', 'default',  $order, null, null, null, $Equipement);
        /*  ********************** Creéation des commandes suivant Horoscope *************************** */
        $order++;
        log::add('horoscope', 'debug', '| ───▶︎ ' . __('Type Horoscope', __FILE__) . ' : ' . $horo_type);
        log::add('horoscope', 'debug', '| ───▶︎ ' . __('Signe', __FILE__) . ' : '  .  $horo_signe);
        if ($this->getConfiguration('signe') != '') {
            horoscope::AddCommand_theme($horo_signe, $order, $horo_type, $Equipement);
        }
        log::add('horoscope', 'debug', '└─────────');
        if (!$this->getIsEnable()) return;
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
        if ($this->getConfiguration('type_horoscope') == '') {
            $this->setConfiguration('type_horoscope', 'traditionnel');
        }
    }

    public function postUpdate()
    {
        if (!$this->getIsEnable()) return;
        $this->getInformations();
    }

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
    public function getHoroscopeCreateCMD($theme_name, $type_horsocope)
    {
        switch ($type_horsocope) {
            case 'traditionnel_condense':
                if ($theme_name === 'Amour' || $theme_name === 'Travail') {
                    $create_cmd = true;
                    //log::add('horoscope', 'debug', "│ {$theme_name} : CREATION");
                } else {
                    $create_cmd = false;
                    //log::add('horoscope', 'debug', "│ {$theme_name} : PAS DE CREATION");
                }
                break;
            default:
                $create_cmd = true;
        }
        return $create_cmd;
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

        /* Création/Update Signe */
        log::add('horoscope', 'debug', '│┌── :fg-success:' . __('Mise à jour du signe de l\'équipement', __FILE__) . ' ::/fg: '   . $this->getName() . ' ──');
        $cmd = $this->getCmd('info', 'signe'); //Mise à jour de la valeur
        if (is_object($cmd)) {
            $cmd->setConfiguration('value', $signe_zodiaque);
            $cmd->save();
            $cmd->event($signe_zodiaque);
        }
        $this->checkAndUpdateCmd('signe', $signe_zodiaque);
        log::add('horoscope', 'debug', '││ ───▶︎ :fg-info:' . __('Mise à jour Signe', __FILE__) . ' :/fg:: ' .  $signe_zodiaque);
        log::add('horoscope', 'debug', '│└─────────');


        $horoscope = self::getHoroscopeForSigne($signe_zodiaque, $type_horsocope, $this->getName());
        log::add('horoscope', 'debug', '│┌── :fg-info:' . __('Mise à jour de l\'équipement', __FILE__) . ' ::/fg: ' . $this->getName() . ' ──');
        foreach ($horoscope['themes'] as $theme_name => $message) {
            if (!is_string($message)) {
                continue;
            }
            log::add('horoscope', 'debug', "││ ───▶︎ {$theme_name} : {$message}");
            //if (is_object($theme_name)) {
            $this->checkAndUpdateCmd($theme_name, $message);
            //}
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
