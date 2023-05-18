<?php

namespace Tests\Driver\OCI;

use Fize\Database\Driver\OCI\OCI;
use PHPUnit\Framework\TestCase;

class TestOCI extends TestCase
{

    public function testBindArrayByName()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');

//        $create = "CREATE TABLE bind_example(name VARCHAR(20))";
//        $oci->parse($create);
//        $oci->execute();

        $create_pkg = "
CREATE OR REPLACE PACKAGE ARRAYBINDPKG1 AS
  TYPE ARRTYPE IS TABLE OF VARCHAR(20) INDEX BY BINARY_INTEGER;
  PROCEDURE iobind(c1 IN OUT ARRTYPE);
END ARRAYBINDPKG1;";
        $stmt = $oci->parse($create_pkg);
        $stmt->execute();

        $create_pkg_body = "
CREATE OR REPLACE PACKAGE BODY ARRAYBINDPKG1 AS
  CURSOR CUR IS SELECT name FROM bind_example;
  PROCEDURE iobind(c1 IN OUT ARRTYPE) IS
    BEGIN
    -- Bulk Insert
    FORALL i IN INDICES OF c1
      INSERT INTO bind_example VALUES (c1(i));

    -- Fetch and reverse
    IF NOT CUR%ISOPEN THEN
      OPEN CUR;
    END IF;
    FOR i IN REVERSE 1..5 LOOP
      FETCH CUR INTO c1(i);
      IF CUR%NOTFOUND THEN
        CLOSE CUR;
        EXIT;
      END IF;
    END LOOP;
  END iobind;
END ARRAYBINDPKG1;";
        $stmt = $oci->parse($create_pkg_body);
        $stmt->execute();

        $stmt = $oci->parse("BEGIN arraybindpkg1.iobind(:c1); END;");
        $array = ["one", "two", "three", "four", "five"];
        $stmt->bindArrayByName(":c1", $array, 5, -1, SQLT_CHR);

        var_dump($array);
        self::assertIsArray($array);
    }

    public function testBindByName()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "INSERT INTO BIND_EXAMPLE VALUES(:text)";
        $stmt = $oci->parse($query);
        $stmt->bindByName(":text", "trailing spaces follow,顺便写点中文。");
        $stmt->execute();
        self::assertTrue(true);
    }

    public function testCancel()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->execute();
        $row0 = $stmt->fetchAssoc();
        var_dump($row0);
        self::assertIsArray($row0);
        $stmt->cancel();
        //游标中断后将无法再调用fetch
//        $row0 = $stmt->fetchAssoc();
//        var_dump($row0);
    }

    public function testClientVersion()
    {
        $version = OCI::clientVersion();
        var_dump($version);
        self::assertIsString($version);
    }

    public function testClose()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->execute();
        $row0 = $stmt->fetchAssoc();
        var_dump($row0);
        $oci->close();
        self::assertIsArray($row0);
    }

    public function testCommit()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "INSERT INTO bind_example VALUES(:text)";
        $stmt = $oci->parse($query);
        $stmt->bindByName(":text", "trailing spaces follow,顺便写点中文。");
        $stmt->execute(OCI_NO_AUTO_COMMIT);
        $result = $oci->commit();
        self::assertTrue($result);
    }

    public function testConnect()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $oci->connect("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        self::assertTrue(true);
    }

    public function testDefineByName()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        while ($stmt->fetch()) {
            echo "name:" . $name . "\n";
        }
        $stmt->freeStatement();
        $oci->close();
        self::assertTrue(true);
    }

    public function testError()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $err = $oci->error();
        var_dump($err);
        self::assertFalse($err);
    }

    public function testExecute()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "INSERT INTO BIND_EXAMPLE VALUES(:text)";
        $stmt = $oci->parse($query);
        $stmt->bindByName(":text", "trailing spaces follow,顺便写点中文。1559");
        $stmt->execute(OCI_NO_AUTO_COMMIT);
        $result = $oci->commit();
        self::assertTrue($result);
    }

    public function testFetchAll()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $sql = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll($results);
        var_dump($rows);
        self::assertIsInt($rows);
        var_dump($results);
        self::assertIsArray($results);
    }

    public function testFetchArray()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $sql = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($sql);
        $stmt->execute();
        while ($row = $stmt->fetchArray(OCI_ASSOC)) {
            var_dump($row);
            self::assertIsArray($row);
        }
    }

    public function testFetchAssoc()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $sql = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($sql);
        $stmt->execute();
        while ($row = $stmt->fetchAssoc()) {
            var_dump($row);
            self::assertIsArray($row);
        }
    }

    public function testFetchObject()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $sql = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($sql);
        $stmt->execute();
        while ($row = $stmt->fetchObject()) {
            var_dump($row);
            self::assertIsObject($row);
        }
    }

    public function testFetchRow()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $sql = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($sql);
        $stmt->execute();
        while ($row = $stmt->fetchRow()) {
            var_dump($row);
            self::assertIsArray($row);
        }
    }

    public function testFetch()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        while ($stmt->fetch()) {
            echo "name:" . $name . "\n";
        }
        $stmt->freeStatement();
        $oci->close();
        self::assertTrue(true);
    }

    public function testFieldIsNull()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        $stmt->fetch();
        $isnull0 = $stmt->fieldIsNull('ISNULL');
        self::assertTrue($isnull0);

        $stmt->fetch();
        $isnull1 = $stmt->fieldIsNull('ISNULL');
        self::assertFalse($isnull1);
    }

    public function testFieldName()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        $name = $stmt->fieldName(1);
        self::assertEquals($name, 'NAME');
    }

    public function testFieldPrecision()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        $precision = $stmt->fieldPrecision(2);
        var_dump($precision);
        self::assertIsInt($precision);
    }

    public function testFieldScale()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        $scale = $stmt->fieldScale(2);
        var_dump($scale);
        self::assertIsInt($scale);
    }

    public function testFieldSize()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        $size = $stmt->fieldSize(2);
        var_dump($size);
        self::assertIsInt($size);
    }

    public function testFieldTypeRaw()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        $type = $stmt->fieldTypeRaw(1);
        var_dump($type);
        self::assertEquals($type, SQLT_CHR);
    }

    public function testFieldType()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        $type = $stmt->fieldType(1);
        var_dump($type);
        self::assertEquals($type, 'VARCHAR2');
    }

    public function testFreeDescriptor()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');

        $rowid = $oci->newDescriptor(OCI_D_ROWID);
        $result = OCI::freeDescriptor($rowid);
        self::assertTrue($result);
    }

    public function testFreeStatement()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->defineByName('NAME', $name);
        $stmt->execute();

        while ($stmt->fetch()) {
            echo "name:" . $name . "\n";
        }
        $stmt->freeStatement();
        $oci->close();
        self::assertTrue(true);
    }

    public function testGetImplicitResultset()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');

        $sql = 'DECLARE
            c1 SYS_REFCURSOR;
        BEGIN
           OPEN c1 FOR SELECT * FROM BIND_EXAMPLE_2 WHERE ISNULL = 1;
           DBMS_SQL.RETURN_RESULT(c1);
           OPEN c1 FOR SELECT * FROM BIND_EXAMPLE_2 WHERE ISNULL IS NULL;
           DBMS_SQL.RETURN_RESULT(c1);
        END;';

        $stid = $oci->parse($sql);
        $stid->execute();

        while (($stid_c = $stid->getImplicitResultset())) {
            echo "<h2>New Implicit Result Set:</h2>\n";
            echo "<table>\n";
            while (($row = $stid_c->fetchArray(OCI_ASSOC + OCI_RETURN_NULLS)) !== false) {
                echo "<tr>\n";
                foreach ($row as $item) {
                    echo "  <td>" . ($item !==null ? htmlentities($item, ENT_QUOTES | ENT_SUBSTITUTE) : "&nbsp;") . "</td>\n";
                }
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
        $stid->freeStatement();
        $oci->close();

        self::assertTrue(true);
    }

    public function testInternalDebug()
    {
        OCI::internalDebug(1);
        self::assertTrue(true);
    }

    public function testLobCopy()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $cblob = $oci->newDescriptor(OCI_DTYPE_LOB);
        //$cblob->write('123456');
        $cblob2 = $oci->newDescriptor(OCI_DTYPE_LOB);
        $result = OCI::lobCopy($cblob2, $cblob);
        self::assertTrue($result);
    }

    public function testLobIsEqual()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $cblob1 = $oci->newDescriptor(OCI_DTYPE_LOB);
        $cblob2 = $oci->newDescriptor(OCI_DTYPE_LOB);
        $result = OCI::lobIsEqual($cblob1, $cblob2);
        self::assertTrue($result);
    }

    public function testNewCollection()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $collection = $oci->newCollection('ODCIVARCHAR2LIST', 'SYS');
        self::assertNotFalse($collection);
    }

    public function testNewConnect()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $oci->newConnect("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        self::assertTrue(true);
    }

    public function testNewCursor()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $curs = $oci->newCursor();
        $stmt = $oci->parse("begin info.output(:data); end;");

        $stmt->bindByName("data", $curs->prototype(), -1, OCI_B_CURSOR);
        $stmt->execute();
        $curs->execute();

        while ($data = $curs->fetchRow()) {
            var_dump($data);
        }

        $stmt->freeStatement();
        $curs->freeStatement();
        $oci->close();
    }

    public function testNewDescriptor()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT ROWID FROM BIND_EXAMPLE_2 WHERE ISNULL = 2";
        $stmt = $oci->parse($query);

        $rowid = $oci->newDescriptor(OCI_D_ROWID);
        $stmt->defineByName("ROWID", $rowid);
        $stmt->execute();
        while ($stmt->fetch()) {
            $nrows = $stmt->numRows();
            echo "$nrows\n";
            $stmt_delete = $oci->parse("DELETE FROM BIND_EXAMPLE_2 WHERE ROWID = :rid");
            $stmt_delete->bindByName(":rid", $rowid, -1, OCI_B_ROWID);
            $stmt_delete->execute();
        }
        $oci->commit();

        $nrows = $stmt->numRows();
        echo "$nrows deleted...\n";
        $oci->freeDescriptor($rowid);
        $stmt->freeStatement();
        $oci->close();

        self::assertIsInt($nrows);
    }

    public function testNumFields()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2 WHERE ISNULL = 2";
        $stmt = $oci->parse($query);
        $stmt->execute();
        $num_fields = $stmt->numFields();
        var_dump($num_fields);
        self::assertIsInt($num_fields);
    }

    public function testNumRows()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2 WHERE ISNULL = 2";
        $stmt = $oci->parse($query);
        $stmt->execute();
        $num_rows = $stmt->numRows();
        var_dump($num_rows);
        self::assertIsInt($num_rows);
    }

    public function testParse()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2 WHERE ISNULL = 2";
        $stmt = $oci->parse($query);
        $stmt->execute();
        $num_rows = $stmt->numRows();
        var_dump($num_rows);
        self::assertIsInt($num_rows);
    }

    public function testPasswordChange()
    {
        $oci = new OCI("OT", "Orcl1234", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->passwordChange("OT", "Orcl1234", 'Orcl123456');
        var_dump($result);
        self::assertTrue($result);
    }

    public function testPconnect()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $oci->pconnect("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        self::assertTrue(true);
    }

    public function testRegisterTafCallback()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->registerTafCallback(function($connection, $event, $type) {
            return 1;
        });
        self::assertTrue($result);
    }

    public function testResult()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->execute();
        while ($stmt->fetch()) {
            $name = $stmt->result('NAME');
            var_dump($name);
            $index = $stmt->result(2);
            var_dump($index);
        }
        $stmt->freeStatement();
        $oci->close();

        self::assertTrue(true);
    }

    public function testRollback()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $query = "INSERT INTO BIND_EXAMPLE VALUES(:text)";
        $stmt = $oci->parse($query);
        $stmt->bindByName(":text", "trailing spaces follow,顺便写点中文。123321");
        $stmt->execute(OCI_NO_AUTO_COMMIT);
        $oci->rollback();  //不会写入
        self::assertTrue(true);
    }

    public function testServerVersion()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $version = $oci->serverVersion();
        var_dump($version);
        self::assertIsString($version);
    }

    public function testSetAction()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->setAction('FizeChanOracle');
        self::assertTrue($result);
    }

    public function testSetCallTimeout()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->setCallTimeout(10000);
        self::assertTrue($result);
    }

    public function testSetClientIdentifier()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->setClientIdentifier('FizeChanOracle');
        self::assertTrue($result);
    }

    public function testSetClientInfo()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->setClientInfo('FizeChanOracle');
        self::assertTrue($result);
    }

    public function testSetDbOperation()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->setDbOperation('FizeChanOperation');
        self::assertTrue($result);
    }

    public function testSetEdition()
    {
        $result = OCI::setEdition('11.11.12');
        self::assertTrue($result);
    }

    public function testSetModuleName()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->setModuleName('FizeChanOperation');
        self::assertTrue($result);
    }

    public function testSetPrefetch()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $result = $stmt->setPrefetch(2);
        self::assertTrue($result);
        $stmt->execute();
        while ($stmt->fetch()) {
            $name = $stmt->result('NAME');
            var_dump($name);
            $index = $stmt->result(2);
            var_dump($index);
        }
        $stmt->freeStatement();
        $oci->close();
    }

    public function testStatementType()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $query = "SELECT * FROM BIND_EXAMPLE_2";
        $stmt = $oci->parse($query);
        $stmt->execute();
        $stat_type = $stmt->statementType();
        self::assertEquals($stat_type, 'SELECT');
        $stmt->freeStatement();
        $oci->close();
    }

    public function testUnregisterTafCallback()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $result = $oci->unregisterTafCallback();
        self::assertTrue($result);
    }
}
