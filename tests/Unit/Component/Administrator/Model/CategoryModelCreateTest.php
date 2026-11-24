<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Tests\Unit\Component\Administrator\Model;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\Input\Input;
use Kunena\Forum\Administrator\Model\CategoryModel;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Categories > New, with a category ticked in the list: the form of a new category in the ticked one (categories.add gives the
 * ticked category as catid of the layout create). The form opened the ticked category itself, and its save changed that category.
 */
class CategoryModelCreateTest extends UnitTestCase
{
    private mixed $savedApplication = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();

        $this->savedApplication = Factory::$application;
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->savedApplication;

        parent::tearDown();
    }

    private function model(array $request): CategoryModel
    {
        $app = $this->createStub(CMSApplication::class);
        $app->method('getInput')->willReturn(new Input($request));
        Factory::$application = $app;

        $model = (new ReflectionClass(CategoryModel::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(CategoryModel::class, 'name'))->setValue($model, 'category');

        return $model;
    }

    public function testTheCategoryOfTheLayoutCreateIsTheParentOfTheNewCategory(): void
    {
        $model = $this->model(['option' => 'com_kunena', 'view' => 'category', 'layout' => 'create', 'catid' => '4']);

        $this->assertSame(0, $model->getState('category.id'));
        $this->assertSame(4, $model->getState('item.parent_id'));
    }

    public function testTheLayoutEditOpensTheCategory(): void
    {
        $model = $this->model(['option' => 'com_kunena', 'view' => 'category', 'layout' => 'edit', 'catid' => '4']);

        $this->assertSame(4, $model->getState('category.id'));
        $this->assertSame(0, $model->getState('item.parent_id', 0));
    }
}
