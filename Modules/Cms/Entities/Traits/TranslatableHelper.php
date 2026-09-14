<?php

namespace Modules\Cms\Entities\Traits;
/**
 *
 */
trait TranslatableHelper
{
    public function translateOrFirst($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        if(! $this->relationLoaded('translations'))
        {
            $this->load('translations');
        }

        $translation = $this->translateOrDefault($locale);

        if(empty($translation))
        {
            $translation = $this->translations->first();
        }

        foreach (['image', 'cover', 'link', 'video', 'video2'] as $attribute) {
            $this->getTranslatedAttribute($attribute, $translation);
        }

        return $translation;
    }

    public function getTranslatedAttribute($attribute, $translation)
    {
        $translationAttributes = $translation->getAttributes();

        if(array_key_exists($attribute, $translationAttributes) && is_null($translation->{$attribute}))
        {
            $translation->{$attribute} = !is_null($hasAttribute = $this->translations->whereNotNull($attribute)->first()) ? $hasAttribute->{$attribute} : null;
        }
    }

    public function translateOrNull($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translation = $this->translate($locale);

        if(empty($translation))
        {
            $translation = NULL;
        }

        return $translation;
    }
}
