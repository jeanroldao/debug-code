using System.IO;
using System;

class testeAnonClass
{
    static void Main()
    {
        
        Console.WriteLine(Dados.GetType().ToString());
        
        var dados = new {Nome="jean", Idade=25};
        
        Console.WriteLine(dados.Nome);
        
        
        // Read in every line in the file.
        using (StreamReader reader = new StreamReader("input.txt"))
        {
            string line;
            while ((line = reader.ReadLine()) != null)
            {
                Console.WriteLine(line);
            }
        }
    }
    
    static Pessoa Dados { get {
        return new PessoaImpl() {
            Nome = "jean",
            Idade = 24
        };
    } }
}


interface Pessoa {
    string Nome {get; set;}
    int Idade {get; set; }
}

class PessoaImpl : Pessoa {
    public string Nome {get; set;}
    public int Idade {get; set; }
}
