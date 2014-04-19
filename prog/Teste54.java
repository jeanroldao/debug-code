import java.io.*;
import java.util.*;
import java.lang.reflect.*;
import javax.script.*;  

public class Teste54 {
	
	public static void main(String[] args) throws Exception {
		ScriptEngineManager manager = new ScriptEngineManager();
        ScriptEngine engine = manager.getEngineByName("JavaScript");
		
		if (engine instanceof Invocable == false) {
			System.out.println(engine + " not Invocable!");
			System.exit(0);
		}
		
		engine.eval("function testFunc(s) { println('inside js: ' + s); return 'arg length: ' + s.length}; ");
		Object result = ((Invocable)engine).invokeFunction("testFunc", "jean roldao");
		System.out.println(result);
		
		///*
		String php_js = new Scanner(new File("PHP.js")).useDelimiter("\\A").next();
		engine.eval(php_js);
		//engine.eval("function execPHP(s) {var engine = new PHP(s); print(engine.vm.OUTPUT_BUFFER);} ");
		engine.eval("function execPHP(s) {(ENV.$F(\"eval\", arguments, $, $Static, this, undefined, ENV, s));} ");
		String php_code = "<?php $t = time(); echo \"oi ($t)\\n\"; ?>";
		Object result2 = ((Invocable)engine).invokeFunction("execPHP", php_code);
        System.out.println(result2);
		//*/
	}
	
}