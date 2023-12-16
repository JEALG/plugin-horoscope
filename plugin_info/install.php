<?php

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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function horoscope_install()
{
    jeedom::getApiKey('horoscope');

    $cron = cron::byClassAndFunction('horoscope', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }

    config::save('functionality::cron::enable', 1, 'horoscope');
}

function horoscope_update()
{
    jeedom::getApiKey('horoscope');

    $cron = cron::byClassAndFunction('horoscope', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }

    config::save('functionality::cron::enable', 1, 'horoscope');

    log::add('horoscope', 'debug', '│ Etape 1/3 : Update(s) nouveautée(s)');

    $plugin = plugin::byId('horoscope');
    $eqLogics = eqLogic::byType($plugin->getId());
    foreach ($eqLogics as $eqLogic) {
        updateLogicalId($eqLogic, 'Nombredechance', null, 'numeric');
    }

    log::add('horoscope', 'debug', '│ Etape 2/3 : Sauvegarde équipement');
    //resave eqLogics for new cmd:
    try {
        $eqs = eqLogic::byType('horoscope');
        foreach ($eqs as $eq) {
            $eq->save();
        }
    } catch (Exception $e) {
        $e = print_r($e, 1);
        log::add('horoscope', 'error', 'horoscope update ERROR : ' . $e);
    }

    log::add('horoscope', 'debug', '│ Etape 3/3 : Mise à jour des équipements');
    //message::add('Plugin Horoscope', 'Merci pour la mise à jour de ce plugin, consultez le changelog.');
    foreach (eqLogic::byType('horoscope') as $horoscope) {
        $horoscope->getInformations();
    }
}

function updateLogicalId($eqLogic, $from, $to = null, $SubType = null)
{
    //  Fonction pour renommer une commande
    $cmd = $eqLogic->getCmd(null, $from);
    if (is_object($cmd)) {
        //changement equipement
        if ($to != null) {
            $cmd->setLogicalId($to);
        }
        //Update sous type
        if ($SubType != null) {
            $cmd->setSubType($SubType);
        }
        $cmd->save();
    }
}

function horoscope_remove()
{
    $cron = cron::byClassAndFunction('horoscope', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}
