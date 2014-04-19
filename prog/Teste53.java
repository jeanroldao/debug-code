import java.io.*;
import java.util.*;
import java.lang.reflect.*;
import javax.script.*;  
import sun.misc.Unsafe;

public class Teste53 {
	
	static int v = 12;
	int num = 123;
	int n2 = 789;

	public static ScriptEngine listScriptEngines() throws Exception {
		ScriptEngineManager mgr = new ScriptEngineManager();
        List<ScriptEngineFactory> factories = mgr.getEngineFactories();

        for (ScriptEngineFactory factory : factories) {

            System.out.println("ScriptEngineFactory Info");

            String engName = factory.getEngineName();
            String engVersion = factory.getEngineVersion();
            String langName = factory.getLanguageName();
            String langVersion = factory.getLanguageVersion();

            System.out.printf("\tScript Engine: %s (%s)\n", engName, engVersion);

            List<String> engNames = factory.getNames();
            for(String name : engNames) {
                System.out.printf("\tEngine Alias: %s\n", name);
            }

            System.out.printf("\tLanguage: %s (%s)\n", langName, langVersion);
			return factory.getScriptEngine();
        }
		return null;
	}
	
	public static void main2(String[] args) throws Exception {
		long v = 9221120237041090560L;
		//long v = 221120237041090560L;
		System.out.println(v);
		
		double d = Double.longBitsToDouble(v);
		System.out.println(d);
		
		//double d2 = 6.880282890558063E-294;
		//System.out.println(d2);
		//System.out.println(d2 * 2);
		long v2 = Double.doubleToLongBits(d);
		System.out.println(v2);
	}
	
	public static void main1(String[] args) throws Exception {
		try {
			Object o = args;
			String s = String.class.cast(o);
			//String s = (String)o;
			System.out.println(s.length());
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
	public static void main(String[] args) throws Exception {
		ScriptEngine engine = listScriptEngines();
		
		//ScriptEngineManager manager = new ScriptEngineManager();
        //ScriptEngine engine = manager.getEngineByName("JavaScript");
		
		System.out.println(engine);
		//engine.eval("var s = 'inside js'; println(s); println(s.length);"); 
		//((Invocable)engine).invoke("fun", "arg1");
	}
	public static void main0(String[] args) throws Exception {
		System.out.println("unsafe java test");
		try {
			Field f = Unsafe.class.getDeclaredField("theUnsafe");
			f.setAccessible(true);
			Unsafe unsafe = (Unsafe) f.get(null);
			Object o = new Teste53();
			System.out.println(unsafe.getInt(o, 16L));
			System.out.println(unsafe.getInt(o, 8L));
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
}