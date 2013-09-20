#include <jni.h>
#include <stdio.h>
#include "Teste13.h"
 
JNIEXPORT void JNICALL Java_Teste13_falar(JNIEnv *env, jobject thisObj, jstring prop) {
	
	const char *inCStrprop = (*env)->GetStringUTFChars(env, prop, NULL);
	if (NULL == inCStrprop) return;
	
	jclass thisClass = (*env)->GetObjectClass(env, thisObj);
	
	// Get the Field ID of the instance variables "message"
	jfieldID fidMessage = (*env)->GetFieldID(env, thisClass, "nome", "Ljava/lang/String;");
	if (NULL == fidMessage) return;

	// String
	// Get the object given the Field ID
	jstring nome = (*env)->GetObjectField(env, thisObj, fidMessage);

	// Create a C-string with the JNI String
	const char *cStrnome = (*env)->GetStringUTFChars(env, nome, NULL);
	if (NULL == cStrnome) return;

	// Step 2: Perform its intended operations
	printf("my %s is %s?\n", inCStrprop, cStrnome);
	
	(*env)->ReleaseStringUTFChars(env, prop, inCStrprop);  // release resources
	(*env)->ReleaseStringUTFChars(env, nome, cStrnome);
}