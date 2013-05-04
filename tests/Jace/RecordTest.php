<?php

namespace Jace;

class User extends Record
{
    protected $_tableName = 'users';

    protected $_data = [
        'id'       => null,
        'name'     => null,
        'birthday' => null,
    ];
}

class RecordTest extends \PHPUnit_Framework_TestCase
{
    protected $_db = null;

    public function setUp()
    {
        $dsn = 'sqlite:' . DB_PATH . '/my.db.sqlite';
        $this->_db = new \PDO($dsn);
        $this->_db->query('CREATE TABLE "users" ("id" INTEGER PRIMARY KEY NOT NULL UNIQUE, "name" VARCHAR, "birthday" VARCHAR)');
    }

    public function tearDown()
    {
        $this->_db->query('DROP TABLE "users"');
    }

    public function testIdShouldBeOneAfterSave()
    {
        
        $user = new User($this->_db);
        $user->name = 'jaceju';
        $user->birthday = '1970-05-01';
        $user->save();
        $this->assertEquals(1, $user->id);
    }

    /**
     * @depends testIdShouldBeOneAfterSave
     */
    public function testItShouldBeSameNameAfterFind()
    {
       
        $user = new User($this->_db);
        $user->name = 'jaceju';
        $user->birthday = '1970-05-01';
        $user->save();

        $user = (new User($this->_db))->find(1);
        $this->assertEquals('jaceju', $user->name);
    }

    /**
     * @depends testItShouldBeSameNameAfterFind
     */
    public function testItShouldBeOtherNameAfterSave()
    {
        
        $user = new User($this->_db);
        $user->name = 'jaceju';
        $user->birthday = '1970-05-01';
        $user->save();

        $user = (new User($this->_db))->find(1);
        $this->assertEquals('jaceju', $user->name);

        $user->name = 'rickysu';
        $user->save();
        $this->assertEquals('rickysu', $user->name);
    }

}
