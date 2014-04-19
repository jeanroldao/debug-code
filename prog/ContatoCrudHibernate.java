import java.sql.Connection;
import java.sql.Date;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

import org.hibernate.FlushMode;
import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.hibernate.cfg.Configuration;

public class ContatoCrudHibernate {
	
	public static class Contato {
		private Integer codigo;
		private String nome;
		private Contato pai;
		private List<Contato> filhos;
		
		public Integer getCodigo() {
			return codigo;
		}
		public void setCodigo(Integer codigo) {
			this.codigo = codigo;
		}
		public String getNome() {
			return nome;
		}
		public void setNome(String nome) {
			this.nome = nome;
		}
		public Contato getPai() {
			return pai;
		}
		public void setPai(Contato pai) {
			this.pai = pai;
		}
		public List<Contato> getFilhos() {
			return filhos;
		}
		public void setFilhos(List<Contato> filhos) {
			this.filhos = filhos;
		}
		
		public String toString() {
			String superString = super.toString().substring(21);
			Integer pai = this.pai != null ? this.pai.codigo : null;
			int total_filhos = filhos != null ? filhos.size() : 0;
			
			//return superString + " (H) {codigo="+codigo+", nome="+nome+", pai="+pai_codigo+"}";
			return superString + " (H) {codigo="+codigo+", nome="+nome+", \n\tpai="+pai+", filhos="+total_filhos+"}";
		}
	}
	
	public static SessionFactory getSessionFactory() {
		return  new Configuration().configure().buildSessionFactory();
	}
	
	public static void main(String[] args) {
		save();
		load();
	}
	
	public static void save() {
		
		String os_name = System.getProperty("os.name");
		
		SessionFactory sessionFactory = getSessionFactory();
		Session session = sessionFactory.openSession();
		
        session.beginTransaction();
		
		Contato beltrano = new Contato();
		beltrano.setNome(os_name +" beltrano");
		
		System.out.println("saving " + beltrano);
		session.save(beltrano);
		System.out.println("ok");
		
		Contato fulano = new Contato();
		fulano.setNome(os_name +" fulano");
		fulano.setPai(beltrano);
		
		System.out.println("saving " + fulano);
		session.save(fulano);
		System.out.println("ok");
		
        session.getTransaction().commit();
        session.close();
		
		sessionFactory.close();
	}
	
	public static void load() {
		
		SessionFactory sessionFactory = getSessionFactory();
		Session session = sessionFactory.openSession();
		
		session.beginTransaction();
		
        List<Contato> result = session.createCriteria(Contato.class).list();
		System.out.println("size: " + result.size());
		for ( Contato contato : result ) {
			System.out.println(contato);
		}
		
		if (result.size() > 3) {
			for (int i = 0; i < 3; i++) {
				Contato c = result.get(i);
				System.out.println("deleting " + c);
				session.delete(c);
				System.out.println("ok");
			}
		}
		
        session.getTransaction().commit();
        session.close();
		
		sessionFactory.close();
	}
	

}
