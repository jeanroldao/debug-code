
import java.util.*;
import java.io.*;
import java.sql.*;

enum Teste22_Coisa extends Enum<Teste22_Coisa> {
    /*public static final*/ COPO /* = new Teste22_Coisa("COPO", 0) */,
    /*public static final*/ PRATO /* = new Teste22_Coisa("PRATO", 1) */,
    /*public static final*/ GARFO /* = new Teste22_Coisa("GARFO", 2) */,
    /*public static final*/ FACA /* = new Teste22_Coisa("FACA", 3) */,
    /*public static final*/ COLHER /* = new Teste22_Coisa("COLHER", 4) */;
    /*synthetic*/ private static final Teste22_Coisa[] $VALUES = new Teste22_Coisa[]{Teste22_Coisa.COPO, Teste22_Coisa.PRATO, Teste22_Coisa.GARFO, Teste22_Coisa.FACA, Teste22_Coisa.COLHER};
    
    public static Teste22_Coisa[] values() {
        return (Teste22_Coisa[])$VALUES.clone();
    }
    
    public static Teste22_Coisa valueOf(String name) {
        return (Teste22_Coisa)Enum.valueOf(Teste22_Coisa.class, name);
    }
    
    private Teste22_Coisa(/*synthetic*/ String $enum$name, /*synthetic*/ int $enum$ordinal) {
        super($enum$name, $enum$ordinal);
    }
}
