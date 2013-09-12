
public class compileonline2 {

     public static void main(String []args) throws Exception {
        
        ((compileonline2) ClassLoader
                     .getSystemClassLoader()
                     .loadClass("compileonline2")
                     .newInstance()).falar("sim");
                     
        ((compileonline2) new Object() {}
                     .getClass()
                     .getEnclosingClass()
                     .newInstance()).falar("nao");
     }
     
     String nome = "Jean";
     
     static int id;
     
     compileonline2 () {
        nome = nome + " " + id++;
        System.out.println("bem vindo.");
     }
     
     void falar(String msg) {
        System.out.println(nome+": "+msg+".");
     }
     
     protected void finalize() {
     	System.out.println(nome+" saiu.");
     }
     
} 

/*
import java.io.PrintStream;

public class compileonline2
{

    public static void main(String args[])
        throws Exception
    {
        ((compileonline2)ClassLoader.getSystemClassLoader().loadClass("compileonline2").newInstance()).falar("sim");
        
    }

    compileonline2()
    {
        nome = "Jean";
        new StringBuilder();
        this;
        JVM INSTR dup_x1 ;
        nome;
        append();
        " ";
        append();
        id++;
        append();
        toString();
        nome;
        System.out.println("bem vindo.");
        return;
    }

    void falar(String s)
    {
        System.out.println((new StringBuilder()).append(nome).append(": ").append(s).append(".").toString());
    }

    protected void finalize()
    {
        System.out.println((new StringBuilder()).append(nome).append(" saiu.").toString());
    }

    String nome;
    static int id;
}*/