<?php

//java.lang.reflect.Array.newArray(Class componentType, int length);
function Java_java_lang_reflect_Array_newArray($componentType, $length) {
	$array = new JavaArray($length, $componentType->getName());
	return $array;
}