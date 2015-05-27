<?php

namespace Craft;

class FakerVariable
{

    public function locale($locale)
    {
        return craft()->faker->setLocale($locale);
    }

    public function fake($extraProviders = array())
    {
        return craft()->faker->factory($extraProviders);
    }
}
