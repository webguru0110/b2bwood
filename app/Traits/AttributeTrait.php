<?php


namespace App\Traits;


use App\Models\AttributeRelationship;
use App\Models\Attribute;
use App\Models\AttributeValue;

trait AttributeTrait
{
    public function attributes(){
        return $this->morphMany(AttributeRelationship::class, 'subject');
    }

    public function seo_attributes() {
        return $this->morphMany(AttributeRelationship::class, 'subject')
                    ->whereHas('attributes', function ($attribute) {
                        $attribute->where('is_schema', true);
                    });
    }

    public function updateAttributes($updated_attributes, $target, $subject_type , $subject_id ) {

        foreach($updated_attributes as $key => $value) {
            $attribute_relationship = $target->attributes->where('attribute_id', $key)->first();
            $attribute = Attribute::find($key);

            if(empty($attribute)) {
                continue;
            }

            if ($value != null) {
                $relationship_id = $value;
                if ($attribute->type != "dropdown" && $attribute->type != "checkbox") {
                    if ($attribute_relationship == null) {
                            $attribute_value = new AttributeValue;
                            $attribute_value->attribute_id = $attribute->id;
                    }else {
                        $attribute_value = AttributeValue::findOrFail($attribute_relationship->attribute_value_id);
                    }
                    if($attribute->type == "text_list"){
                        array_pop($value);
                        $attribute_value->values = json_encode($value);
                        $attribute_value->save();
                        $relationship_id = $attribute_value->id;
                    }else{
                        $attribute_value->values = $value;
                        $attribute_value->save();
                        $relationship_id = $attribute_value->id;
                    }
                }

                if ($attribute->type != "checkbox") {
                    if ($attribute_relationship == null) {
                        $attribute_relationship = new AttributeRelationship;
                        $attribute_relationship->subject_type = $subject_type;
                        $attribute_relationship->subject_id = $subject_id;
                        $attribute_relationship->attribute_id = $key;
                    }
                    $attribute_relationship->attribute_value_id = $relationship_id;
                    $attribute_relationship->save();
                }else {
                    foreach($target->attributes->where('attribute_id', $key)->whereNotIn('attribute_value_id', $value) as $relation) {
                        $relation->delete();
                    }
                    foreach($value as $index => $option) {
                        if (count($target->attributes->where('attribute_id', $key)->where('attribute_value_id', $option)) == 0) {
                            $attribute_relationship = new AttributeRelationship;
                            $attribute_relationship->subject_type = $subject_type;
                            $attribute_relationship->subject_id = $subject_id;
                            $attribute_relationship->attribute_id = $key;
                            $attribute_relationship->attribute_value_id = $option;
                            $attribute_relationship->save();
                        }
                    }
                }
            }else {
                if ($attribute->type == "checkbox") {
                    foreach($target->attributes->where('attribute_id', $key) as $relation) {
                        $relation->delete();
                    }
                }else if ($attribute_relationship != null){
                    $attribute_value = AttributeValue::findOrFail($attribute_relationship->attribute_value_id);
                    $attribute_relationship->delete();
                    $attribute_value->delete();
                }
            }
        }
    }
}
