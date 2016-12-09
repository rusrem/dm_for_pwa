<?php
/**
 * /_includes/updates/4.02.000-current.inc.php
 *
 * This file is part of DomainMOD, an open source domain and internet asset manager.
 * Copyright (c) 2010-2016 Greg Chetcuti <greg@chetcuti.com>
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
?>
<?php //@formatter:off

// upgrade database from 4.02.000 to 4.02.001
if ($current_db_version === '4.02.000') {

    $sql = "UPDATE currencies
            SET symbol = '₺'
            WHERE currency = 'TRY'";
    $result = mysqli_query($connection, $sql) or $error->outputOldSqlError($connection);

    $sql = "ALTER TABLE `settings`
            CHANGE `smtp_port` `smtp_port` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '587'";
    $result = mysqli_query($connection, $sql) or $error->outputOldSqlError($connection);

    $sql = "UPDATE settings
            SET db_version = '4.02.001',
                update_time = '" . $time->stamp() . "'";
    $result = mysqli_query($connection, $sql) or $error->outputOldSqlError($connection);

    $current_db_version = '4.02.001';

}

//@formatter:on
