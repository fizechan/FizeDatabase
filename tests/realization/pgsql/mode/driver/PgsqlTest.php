<?php

namespace realization\pgsql\mode\driver;

use fize\db\realization\pgsql\mode\driver\Pgsql;
use PHPUnit\Framework\TestCase;

class PgsqlTest extends TestCase
{

    public function test__construct()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        var_dump($db);
        self::assertTrue(true);
    }

    public function test__destruct()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        var_dump($db);
        $db = null;
        var_dump($db);
        self::assertTrue(true);
    }

    public function testAffectedRows()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('DELETE FROM "user" WHERE sex=1');
        $affected_rows = $result->affectedRows();
        var_dump($affected_rows);
        self::assertIsInt($affected_rows);
    }

    public function testCancelQuery()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->sendQuery('SELECT * FROM "user"');
        $bool = $db->cancelQuery();
        self::assertTrue($bool);
    }

    public function testClientEncoding()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $encoding = $db->clientEncoding();
        var_dump($encoding);
        self::assertIsString($encoding);
    }

    public function testClose()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->close();
        self::assertTrue(true);
    }

    public function testConnectPoll()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $polls = $db->connectPoll();
        var_dump($polls);
        self::assertIsInt($polls);
    }

    public function testConnect()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->connect("host=192.168.56.101 port=5432 dbname=gmtest user=postgres password=123456");  //切换连接
        var_dump($db);
        self::assertTrue(true);
    }

    public function testConnectionBusy()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $busy = $db->connectionBusy();
        var_dump($busy);
        self::assertIsBool($busy);
    }

    public function testConnectionReset()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $bool = $db->connectionReset();
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testConnectionStatus()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $status = $db->connectionStatus();
        var_dump($status);
        self::assertIsInt($status);
    }

    public function testConsumeInput()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $bool = $db->consumeInput();
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testConvert()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $data = [
            'name'     => "!乱/七\八'糟\"的*字?符%串`一#大@堆(",
            'add_time' => time()
        ];
        $data = $db->convert('user', $data);
        var_dump($data);
        self::assertIsArray($data);
    }

    public function testCopyFrom()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");

        $data = [
            '1	陈峰展	2	\N',
            '3	xxoo	3	123456',
        ];
        $bool = $db->copyFrom('"user"', $data);
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testCopyTo()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $rows = $db->copyTo('"user"');
        var_dump($rows);
        self::assertIsArray($rows);
    }

    public function testDbname()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $dbname = $db->dbname();
        var_dump($dbname);
        self::assertEquals($dbname, 'gmtest');
    }

    public function testDelete()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $bool = $db->delete('user', ['sex' => 3]);  //delete方法不需要转义符
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testEndCopy()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("CREATE TABLE bar (a INT4, b CHAR(16), d FLOAT8)");
        $db->query("COPY bar FROM stdin");
        $db->putLine("3\thello world\t4.5\n");
        $db->putLine("4\tgoodbye world\t7.11\n");
        $db->putLine("\.\n");
        $bool = $db->endCopy();
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testEscapeBytea()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $data = file_get_contents(__DIR__ . '/PgsqlTest.php');
        $string = $db->escapeBytea($data);
        var_dump($string);
        self::assertIsString($string);
    }

    public function testEscapeIdentifier()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $data = "!乱/七\八'糟\"的*字?符%串`一#大@堆(";
        $string = $db->escapeIdentifier($data);
        var_dump($string);
        self::assertIsString($string);
    }

    public function testEscapeLiteral()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $data = "!乱/七\八'糟\"的*字?符%串`一#大@堆(";
        $string = $db->escapeLiteral($data);
        var_dump($string);
        self::assertIsString($string);
    }

    public function testEscapeString()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $data = "!乱/七\八'糟\"的*字?符%串`一#大@堆(";
        $string = $db->escapeString($data);
        var_dump($string);
        self::assertIsString($string);
    }

    public function testExecute()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->prepare('my_query', 'DELETE FROM "user" WHERE sex=$1');
        $result = $db->execute('my_query', [0]);
        $affected_rows = $result->affectedRows();
        var_dump($affected_rows);
        self::assertIsInt($affected_rows);
    }

    public function testFetchAllColumns()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $columns = $result->fetchAllColumns(1);
        var_dump($columns);
        self::assertIsArray($columns);
    }

    public function testFetchAll()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $rows = $result->fetchAll();
        var_dump($rows);
        self::assertIsArray($rows);
    }

    public function testFetchArray()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "bar"');
        while ($row = $result->fetchArray()) {
            var_dump($row);
            self::assertIsArray($row);
        }
    }

    public function testFetchAssoc()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "bar"');
        while ($row = $result->fetchAssoc()) {
            var_dump($row);
            self::assertIsArray($row);
        }
    }

    public function testFetchObject()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "bar"');
        while ($row = $result->fetchObject()) {
            var_dump($row);
            self::assertIsObject($row);
        }
    }

    public function testFetchResult()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "bar"');
        $value1 = $result->fetchResult(0, 2);
        self::assertEquals($value1, 4.5);
        $value2 = $result->fetchResult(1, 'd');
        self::assertEquals($value2, 7.11);
    }

    public function testFetchRow()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "bar"');
        while ($row = $result->fetchRow()) {
            var_dump($row);
            self::assertIsArray($row);
        }
    }

    public function testFieldIsNull()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $boll1 = $result->fieldIsNull(0, 'sex');
        self::assertEquals($boll1, 0);
        $boll2 = $result->fieldIsNull(0, 'add_time');
        self::assertEquals($boll2, 1);
    }

    public function testFieldName()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $field_name = $result->fieldName(1);
        var_dump($field_name);
        self::assertEquals($field_name, 'name');
    }

    public function testFieldNum()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $field_num = $result->fieldNum('name');
        var_dump($field_num);
        self::assertEquals($field_num, 1);
    }

    public function testFieldPrtlen()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $len = $result->fieldPrtlen(0, 'name');
        var_dump($len);
        self::assertIsInt($len);
    }

    public function testFieldSize()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $size = $result->fieldSize(1);
        var_dump($size);
        self::assertIsInt($size);
    }

    public function testFieldTable()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $table = $result->fieldTable(1);
        var_dump($table);
        self::assertIsString($table);
    }

    public function testFieldTypeOid()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $oid = $result->fieldTypeOid(1);
        var_dump($oid);
        self::assertIsInt($oid);
    }

    public function testFieldType()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $type = $result->fieldType(1);
        var_dump($type);
        self::assertIsString($type);
    }

    public function testFlush()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query('SELECT * FROM "user"');
        $result = $db->flush();
        var_dump($result);
        self::assertTrue(true);
    }

    public function testFreeResult()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $result->freeResult();
        self::assertTrue(true);
    }

    public function testGetNotify()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query('LISTEN author_updated;');
        $notify = $db->getNotify();
        var_dump($notify);
        self::assertTrue(true);
    }

    public function testGetPid()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $pid = $db->getPid();
        var_dump($pid);
        self::assertIsInt($pid);
    }

    public function testGetResult()
    {
        $dbh = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $query = "SELECT pg_sleep(3)";
        if (!$dbh->connectionBusy()) {
            $dbh->sendQuery($query);
            print "Sent query, waiting: ";
            while ($dbh->connectionBusy()) {
                print ".";
                flush();
            }
            $res = $dbh->getResult();
            print "<br>\n";
            var_dump($res);
        }
        $dbh->close();
        self::assertTrue(true);
    }

    public function testHost()
    {
        $dbh = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $host = $dbh->host();
        var_dump($host);
        self::assertIsString($host);
    }

    public function testInsert()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $data = [
            'name'     => "!乱/七\八'糟\"的*字?符%串`一#大@堆(890723",
            'sex'      => 3,
            'add_time' => time()
        ];
        $result = $db->insert('user', $data);
        var_dump($result);
        self::assertTrue(true);
    }

    public function testLastError()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $error = $db->lastError();
        var_dump($error);
        self::assertIsString($error);
    }

    public function testLastNotice()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $notice = $db->lastNotice();
        var_dump($notice);
        self::assertIsString($notice);
    }

    public function testLastOid()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query("INSERT INTO bar VALUES (5, '这是什么！', 4.45)");
        $oid = $result->lastOid();
        var_dump($oid);
        self::assertTrue(true);
    }

    public function testLoClose()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loCreate();
        $lo = $db->loOpen($oid, 'rw');
        var_dump($lo);
        self::assertIsObject($lo);
        $bool = $lo->loClose();
        $db->query("COMMIT");
        self::assertTrue($bool);
    }

    public function testLoCreate()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loCreate();
        var_dump($oid);
        $lo = $db->loOpen($oid, 'rw');
        $lo->loClose();
        $db->query("COMMIT");
        self::assertIsInt($oid);
    }

    public function testLoExport()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loCreate();
        $lo = $db->loOpen($oid, 'w');
        $lo->loWrite(file_get_contents(__DIR__ . './PgsqlTest.php'));
        $lo->loClose();
        $bool = $db->loExport($oid, 'D:/test.php');
        $db->query("COMMIT");
        self::assertTrue($bool);
    }

    public function testLoImport()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loImport(__DIR__ . './PgsqlTest.php');
        var_dump($oid);
        $db->query("COMMIT");
        self::assertIsInt($oid);
    }

    public function testLoOpen()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loCreate();
        var_dump($oid);
        $lo = $db->loOpen($oid, 'rw');
        var_dump($lo);
        self::assertIsObject($lo);
        $lo->loClose();
        $db->query("COMMIT");
        self::assertIsInt($oid);
    }

    public function testLoReadAll()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loImport(__DIR__ . './PgsqlTest.php');
        $lo = $db->loOpen($oid, 'r');
        $count = $lo->loReadAll();
        $db->query("COMMIT");
        self::assertIsInt($count);
    }

    public function testLoRead()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loImport(__DIR__ . './PgsqlTest.php');
        $lo = $db->loOpen($oid, 'r');
        $string = $lo->loRead(100);
        $db->query("COMMIT");
        var_dump($string);
        self::assertIsString($string);
    }

    public function testLoSeek()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loImport(__DIR__ . './PgsqlTest.php');
        $lo = $db->loOpen($oid, 'r');
        $bool = $lo->loSeek(100);
        $string = $lo->loRead(100);
        $db->query("COMMIT");
        var_dump($string);
        self::assertIsString($string);
        self::assertTrue($bool);
    }

    public function testLoTell()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loImport(__DIR__ . './PgsqlTest.php');
        $lo = $db->loOpen($oid, 'r');
        $lo->loSeek(100);
        $tell = $lo->loTell();
        $db->query("COMMIT");
        var_dump($tell);
        self::assertIsInt($tell);
        self::assertEquals($tell, 100);
    }

    public function testLoTruncate()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loImport(__DIR__ . './PgsqlTest.php');
        $lo = $db->loOpen($oid, 'w');
        $bool = $lo->loTruncate(100);
        $db->query("COMMIT");
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testLoUnlink()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loCreate();
        $lo = $db->loOpen($oid, 'w');
        $lo->loWrite(file_get_contents(__DIR__ . './PgsqlTest.php'));
        $lo->loClose();
        $bool = $db->loUnlink($oid);
        $db->query("COMMIT");
        self::assertTrue($bool);
    }

    public function testLoWrite()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("BEGIN");
        $oid = $db->loCreate();
        $lo = $db->loOpen($oid, 'w');
        $count = $lo->loWrite(file_get_contents(__DIR__ . './PgsqlTest.php'));
        $lo->loClose();
        $db->query("COMMIT");
        var_dump($count);
        self::assertIsInt($count);
    }

    public function testMetaData()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $data = $db->metaData('user');
        var_dump($data);
        self::assertIsArray($data);
    }

    public function testNumFields()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $num = $result->numFields();
        var_dump($num);
        self::assertIsInt($num);
    }

    public function testNumRows()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $num = $result->numRows();
        var_dump($num);
        self::assertIsInt($num);
    }

    public function testOptions()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $options = $db->options();
        var_dump($options);
        self::assertIsString($options);
    }

    public function testParameterStatus()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $string = $db->parameterStatus('server_encoding');
        var_dump($string);
        self::assertIsString($string);
    }

    public function testPconnect()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456", true);
        var_dump($db);
        self::assertIsObject($db);
    }

    public function testPing()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456", true);
        $bool = $db->ping();
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testPort()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456", true);
        $port = $db->port();
        var_dump($port);
        self::assertIsNumeric($port);
    }

    public function testPrepare()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->prepare('my_query', 'DELETE FROM "user" WHERE sex=$1');
        var_dump($result);
        $result = $db->execute('my_query', [0]);
        $affected_rows = $result->affectedRows();
        var_dump($affected_rows);
        self::assertIsInt($affected_rows);
    }

    public function testPutLine()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->query("CREATE TABLE bar2 (a INT4, b CHAR(16), d FLOAT8)");
        $db->query("COPY bar2 FROM stdin");
        $db->putLine("3\thello world\t4.5\n");
        $db->putLine("4\tgoodbye world\t7.11\n");
        $db->putLine("\.\n");
        $bool = $db->endCopy();
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testQueryParams()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->queryParams('DELETE FROM "user" WHERE sex=$1', [0]);
        $affected_rows = $result->affectedRows();
        var_dump($affected_rows);
        self::assertIsInt($affected_rows);
    }

    public function testQuery()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        var_dump($result);
        self::assertIsObject($result);
    }

    public function testResultErrorField()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->sendQuery('SELECT * FROM doesnotexist');
        $res1 = $db->getResult();
        $report = $res1->resultErrorField(PGSQL_DIAG_SQLSTATE);
        var_dump($report);
        self::assertIsString($report);
    }

    public function testResultError()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->sendQuery('SELECT * FROM doesnotexist');
        $res1 = $db->getResult();
        $error = $res1->resultError();
        var_dump($error);
        self::assertIsString($error);
    }

    public function testResultSeek()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $result->resultSeek(1);
        $row = $result->fetchRow();
        var_dump($row);
        self::assertIsArray($row);
    }

    public function testResultStatus()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $result = $db->query('SELECT * FROM "user"');
        $status = $result->resultStatus();
        var_dump($status);
        self::assertIsString($status);
    }

    public function testSelect()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $rec = $db->select('user', ['sex' => 3]);
        var_dump($rec);
        self::assertIsArray($rec);
        $rec = $db->select('user', ['sex' => 3], PGSQL_DML_STRING);
        var_dump($rec);
        self::assertIsString($rec);
    }

    public function testSendExecute()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");

        if (!$db->connectionBusy()) {
            $db->sendPrepare("my_query", 'SELECT * FROM "user" WHERE sex = $1');
            $res1 = $db->getResult();
            var_dump($res1);
        }

        if (!$db->connectionBusy()) {
            $db->sendExecute("my_query", [2]);
            $res2 = $db->getResult();
            $rows = $res2->fetchAll();
            var_dump($rows);
            self::assertIsArray($rows);
        }

        if (!$db->connectionBusy()) {
            $db->sendExecute("my_query", [3]);
            $res3 = $db->getResult();
            $rows = $res3->fetchAll();
            var_dump($rows);
            self::assertIsArray($rows);
        }
    }

    public function testSendPrepare()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");

        if (!$db->connectionBusy()) {
            $db->sendPrepare("my_query", 'SELECT * FROM "user" WHERE sex = $1');
            $res1 = $db->getResult();
            var_dump($res1);
        }

        if (!$db->connectionBusy()) {
            $db->sendExecute("my_query", [2]);
            $res2 = $db->getResult();
            $rows = $res2->fetchAll();
            var_dump($rows);
            self::assertIsArray($rows);
        }

        if (!$db->connectionBusy()) {
            $db->sendExecute("my_query", [3]);
            $res3 = $db->getResult();
            $rows = $res3->fetchAll();
            var_dump($rows);
            self::assertIsArray($rows);
        }
    }

    public function testSendQueryParams()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $bool = $db->sendQueryParams('DELETE FROM "user" WHERE sex=$1', [0]);
        var_dump($bool);
        self::assertTrue($bool);

        $result = $db->getResult();
        $num = $result->affectedRows();
        self::assertIsInt($num);
    }

    public function testSendQuery()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $bool = $db->sendQuery('DELETE FROM "user" WHERE sex=0');
        var_dump($bool);
        self::assertTrue($bool);

        $result = $db->getResult();
        $num = $result->affectedRows();
        self::assertIsInt($num);
    }

    public function testSetClientEncoding()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $bool = $db->setClientEncoding('SQL_ASCII');
        var_dump($bool);
        self::assertEquals($bool, 0);
    }

    public function testSetErrorVerbosity()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $rst = $db->setErrorVerbosity(PGSQL_ERRORS_TERSE);
        var_dump($rst);
        self::assertEquals($rst, PGSQL_ERRORS_TERSE);
    }

    public function testSocket()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $socket = $db->socket();
        var_dump($socket);
        self::assertIsResource($socket);
    }

    public function testTrace()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $bool = $db->trace('D:/trace.log');
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testTransactionStatus()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $status = $db->transactionStatus();
        var_dump($status);
        self::assertIsInt($status);
    }

    public function testTty()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $tty = $db->tty();
        var_dump($tty);
        self::assertIsString($tty);
    }

    public function testUnescapeBytea()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $data = file_get_contents(__DIR__ . '/PgsqlTest.php');
        $string = $db->escapeBytea($data);
        $string = Pgsql::unescapeBytea($string);
        var_dump($string);
        self::assertIsString($string);
    }

    public function testUntrace()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $bool = $db->untrace();
        var_dump($bool);
        self::assertTrue($bool);
    }

    public function testUpdate()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $rec1 = $db->update('user', ['sex' => 4], ['id' => 4]);
        var_dump($rec1);
        self::assertTrue($rec1);

        $rec2 = $db->update('user', ['sex' => 5], ['id' => 5], PGSQL_DML_STRING);
        var_dump($rec2);
        self::assertIsString($rec2);
    }

    public function testVersion()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $version = $db->version();
        var_dump($version);
        self::assertIsArray($version);
    }
}
