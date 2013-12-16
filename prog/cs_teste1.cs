using System.IO;
using System;

public class cs_teste1
{
    public string Name {get;set;}
	
	public cs_teste1(string name) {
		Name = name;
	}
	
	public void Speak(string what = "nothing") {
		Console.WriteLine(Name + ": " + what);
	}
	
	public override string ToString() {
		return "{"+Name+"}";
	}
	
	public static void DoSpeak(dynamic o) {
		o.Speak();
		o.Speak("1");
	}
	
	public static void Main()
    {
		var name = "some name";
		var name2 = createSomeName();
		Console.WriteLine(name);
		Console.WriteLine(name2);
		
		DoSpeak(new cs_teste1(name2));
    }
	
	public static string createSomeName() {
		return "some name created";
	}
}