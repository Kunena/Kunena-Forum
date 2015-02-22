= How to convert MVC into HMVC

Converting Joomla legacy MVC into HMVC is quite easy and can be done in several smaller steps. This document will give
you instructions on how to do that conversion quickly and without too much hassle.

HMVC implementation has several ideological differences to the current Joomla MVC implementation, including:

* Constant JPATH_COMPONENT shouldn't be used anymore
* Controllers are auto-loadable and located in component/com_kunena/controller
* Controllers are called ComponentKunenaControllerXxxYyyZzz
* Display controllers contain most of the code from JViewLegacy sub-classes
* Any object can be a model -- legacy models are not needed anymore, but can still be used
* View classes are only data containers, they shouldn't contain any application logic
* View classes are not needed anymore, by default KunenaLayout class is used

== Moving template files to their new locations

Create directory ./template/application into your component. Then replicate the same directory structure as in ./views
and copy all template files into their proper locations.

Then go through all view classes and change following line in display() method:

    parent::display($tpl);

into this:

    $name = ucfirst($this->getName());
    echo KunenaLayout::factory("Application/{$name}")->setProperties($this->getProperties(false))->setLayout($this->getLayout());

That's it! Your component should be fully operational and using KunenaLayouts instead of legacy template files.

== Moving code from JViewLegacy into its proper location

Next step is to move display() related code from JViewLegacy class into the new controller. Most of the code should go
into before() function. Only display code for layout remains in display().

The only code that remains in the legacy view is this:

    public function display($tpl = null)
    {
        $name = ucfirst($this->getName());
        echo KunenaRequest::factory("Application/{$name}/Display")->execute()->setLayout($this->getLayout());
    }

