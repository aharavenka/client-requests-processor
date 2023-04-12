<?php

interface LeadProcessorInterface
{
    public const unsupportedCategories = ['Car wash', 'Car insurance'];

    public function process(LeadGenerator\Lead $lead): void;
}
