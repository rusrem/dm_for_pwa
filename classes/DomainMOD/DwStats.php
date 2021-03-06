<?php
/**
 * /classes/DomainMOD/DwStats.php
 *
 * This file is part of DomainMOD, an open source domain and internet asset manager.
 * Copyright (c) 2010-2017 Greg Chetcuti <greg@chetcuti.com>
 *
 * Project: http://domainmod.org   Author: http://chetcuti.com
 *
 * DomainMOD is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * DomainMOD is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with DomainMOD. If not, see
 * http://www.gnu.org/licenses/.
 *
 */
//@formatter:off
namespace DomainMOD;

class DwStats
{

    public function updateServerStats($dbcon, $result)
    {

        while ($row = mysqli_fetch_object($result)) {

            $total_dw_accounts = $this->getTotals($dbcon, $row->id, 'dw_accounts');
            $total_dw_dns_zones = $this->getTotals($dbcon, $row->id, 'dw_dns_zones');
            $total_dw_dns_records = $this->getTotals($dbcon, $row->id, 'dw_dns_records');
            $this->updateServerTotals($dbcon, $row->id, $total_dw_accounts, $total_dw_dns_zones,
                $total_dw_dns_records);

        }

    }

    public function getTotals($dbcon, $server_id, $table)
    {

        $sql = "SELECT count(*) AS total
                FROM `" . $table . "`
                WHERE server_id = '" . $server_id . "'";
        $result = mysqli_query($dbcon, $sql);

        $total = '';

        while ($row = mysqli_fetch_object($result)) {

            $total = $row->total;

        }

        return $total;

    }

    public function updateServerTotals($dbcon, $server_id, $total_dw_accounts, $total_dw_dns_zones,
                                       $total_dw_dns_records)
    {

        $sql_update = "UPDATE dw_servers
                       SET dw_accounts = '" . $total_dw_accounts . "',
                           dw_dns_zones = '" . $total_dw_dns_zones . "',
                           dw_dns_records = '" . $total_dw_dns_records . "'
                       WHERE id = '" . $server_id . "'";
        mysqli_query($dbcon, $sql_update);

        return true;

    }

    public function updateDwTotalsTable($dbcon)
    {

        $accounts = new DwAccounts();
        $zones = new DwZones();
        $records = new DwRecords();

        $this->deleteTotalsTable($dbcon);
        $this->recreateDwTotalsTable($dbcon);
        $total_dw_servers = $this->getTotalDwServers($dbcon);
        $total_dw_accounts = $accounts->getTotalDwAccounts($dbcon);
        $total_dw_zones = $zones->getTotalDwZones($dbcon);
        $total_dw_records = $records->getTotalDwRecords($dbcon);
        $this->updateTable($dbcon, $total_dw_servers, $total_dw_accounts, $total_dw_zones, $total_dw_records);

        return true;

    }

    public function deleteTotalsTable($dbcon)
    {

        $sql = "DROP TABLE IF EXISTS dw_server_totals";
        mysqli_query($dbcon, $sql);

        return true;

    }

    public function recreateDwTotalsTable($dbcon)
    {

        $sql = "CREATE TABLE IF NOT EXISTS `dw_server_totals` (
                    `id` INT(10) NOT NULL AUTO_INCREMENT,
                    `dw_servers` INT(10) NOT NULL,
                    `dw_accounts` INT(10) NOT NULL,
                    `dw_dns_zones` INT(10) NOT NULL,
                    `dw_dns_records` INT(10) NOT NULL,
                    `insert_time` DATETIME NOT NULL,
                    PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
        mysqli_query($dbcon, $sql);

        return true;

    }

    public function getTotalDwServers($dbcon)
    {

        $total_dw_servers = '';

        $sql_servers = "SELECT count(*) AS total_dw_servers
                        FROM `dw_servers`";
        $result_servers = mysqli_query($dbcon, $sql_servers);

        while ($row_servers = mysqli_fetch_object($result_servers)) {

            $total_dw_servers = $row_servers->total_dw_servers;

        }

        return $total_dw_servers;

    }

    public function updateTable($dbcon, $total_dw_servers, $total_dw_accounts, $total_dw_dns_zones, $total_dw_records)
    {

        $time = new Time();

        $sql_insert = "INSERT INTO dw_server_totals
                       (dw_servers, dw_accounts, dw_dns_zones, dw_dns_records, insert_time)
                       VALUES
                       ('" . $total_dw_servers . "', '" . $total_dw_accounts . "', '" . $total_dw_dns_zones . "',
                        '" . $total_dw_records . "', '" . $time->stamp() . "')";
        mysqli_query($dbcon, $sql_insert);

        return true;

    }

    public function getServerTotals($dbcon)
    {

        $temp_dw_accounts = '0';
        $temp_dw_dns_zones = '0';
        $temp_dw_dns_records = '0';

        $sql = "SELECT dw_accounts, dw_dns_zones, dw_dns_records
                FROM dw_server_totals";
        $result = mysqli_query($dbcon, $sql);

        while ($row = mysqli_fetch_object($result)) {

            $temp_dw_accounts = $row->dw_accounts;
            $temp_dw_dns_zones = $row->dw_dns_zones;
            $temp_dw_dns_records = $row->dw_dns_records;

        }

        return array($temp_dw_accounts, $temp_dw_dns_zones, $temp_dw_dns_records);

    }

} //@formatter:on
