<?php
namespace Kristuff\Miniweb\Tests\Http;


class UserModelTest extends \PHPUnit\Framework\TestCase
{

    public function testCreateTable()
    {
        $params = ['driver' => 'sqlite', 'database' => ':memory:'];
        $db = new \Kristuff\Patabase\Database($params);

        $this->assertTrue(\Kristuff\Miniweb\Auth\Model\UserModel::createTable($db));
        $this->assertTrue(\Kristuff\Miniweb\Auth\Model\UserSettingsModel::createTableSettings($db));
        
    }
}