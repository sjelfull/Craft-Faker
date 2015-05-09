<?php

namespace Craft;

class FakerVariable
{

    public function locale($locale)
    {
        return craft()->faker->setLocale($locale);
    }

    public function fake($extraProviders = [])
    {
        return craft()->faker->factory($extraProviders);
    }
}