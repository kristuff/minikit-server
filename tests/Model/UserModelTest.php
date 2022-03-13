<?php
namespace Kristuff\Minikit\Tests\Http;


class UserModelTest extends \PHPUnit\Framework\TestCase
{

    public function testCreateTable()
    {
        $params = ['driver' => 'sqlite', 'database' => ':memory:'];
        $db = new \Kristuff\Patabase\Database($params);

        $this->assertTrue(\Kristuff\Minikit\Auth\Model\UserModel::createTable($db));
        $this->assertTrue(\Kristuff\Minikit\Auth\Model\UserSettingsModel::createTableSettings($db));
        
    }
}