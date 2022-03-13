<?php

use Kristuff\Minikit\Data\Model\DatabaseModel;

class FooDatabaseModel extends DatabaseModel
{

    public static function tryToDoSomethingWithDatabase()
    {
        return self::database()->tableExists('foo');
    }


    public static function tryCreateTable()
    {
        return self::database()->table('test')
                               ->create()
                               ->ifNotExists()
                               ->column('id', 'int', 'pk')
                               ->execute();
    }

}