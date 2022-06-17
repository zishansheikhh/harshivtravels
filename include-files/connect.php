<?php
	
	function MysqlOpen()
    {
        $DBCon = mysqli_connect(_DB_HOST, _DB_USER, _DB_PASSWORD, _DB_NAME);
        if (!$DBCon)
        {
            die('Could not connect: ' . mysqli_connect_error());
        }
        return $DBCon;
    }

    function MysqlClose()
    {
        global $DBCon;
        mysqli_close($DBCon);
    }

	function MysqlQuery($query)
    {
        global $DBCon;
        $IsSelect = strtoupper(substr($query, 0, 6)) == 'SELECT' ? true : false;
        if($IsSelect)
        {
            //  Character set modified to make sure both German Umlaut and Hindi
            //  is retrived and returned correctly
            mysqli_set_charset($DBCon, 'utf8mb4');
        }

        $res = mysqli_query($DBCon, $query);
        if(mysqli_error($DBCon) != '')
        {
            $res = mysqli_error($DBCon);
        }
        return $res;
    }

    function MysqlFetchAll($resource, $pk = '')
    {
        $data = array();

        for(; $row = mysqli_fetch_assoc($resource); )
        {
            if($pk == '')
            {
                $data[] = $row;    
            }
            else
            {
                $data[$row[$pk]] = $row;
            }
        }

        return $data;
    }

    function MysqlAffectedRows()
    {
        global $DBCon;
        $AffectedRows = mysqli_affected_rows($DBCon);
        return $AffectedRows;
    }

    function MysqlInsertID()
    {
        global $DBCon;
        $InsertID = mysqli_insert_id($DBCon);
        return $InsertID;
    }

    function MysqlDisableAutoCommit()
    {
        global $DBCon;
        mysqli_autocommit($DBCon, FALSE);
    }

    function MysqlCommit()
    {
        global $DBCon;
        mysqli_commit($DBCon);
    }

    function MysqlRollback()
    {
        global $DBCon;
        mysqli_rollback($DBCon);
    }

    function PrepareInsertQuery($TableName, $KeyValuesArray)
    {
        foreach($KeyValuesArray as $key => $val)
        {
            if(strpos($val, "'") !== false && strpos($val, "\'") === false)
            {
                $KeyValuesArray[$key] = addslashes($val);
            }
        }
        $query = "INSERT INTO ".$TableName;
        $query .= " (".implode(',', array_keys($KeyValuesArray)).")";
        $query .= " VALUES ('".implode("','", $KeyValuesArray)."')";
        return $query;
    }

    function PrepareUpdateQuery($TableName, $KeyValuesArray, $WhereCondition, $Limit = 1)
    {
        foreach($KeyValuesArray as $key => $val)
        {
            if(strpos($val, "'") !== false && strpos($val, "\'") === false)
            {
                $KeyValuesArray[$key] = addslashes($val);
            }
        }
        $query = "UPDATE ".$TableName." SET ";
        foreach($KeyValuesArray as $key => $value)
        {
            $arr[] = $key. " = '".$value."'";
        }
        $query .= implode(',', $arr);
        $query .= " WHERE ".$WhereCondition." LIMIT ".$Limit;
        return $query;
    }

    $DBCon = MysqlOpen();
	date_default_timezone_set("Asia/Calcutta");		//Set time zone

?>