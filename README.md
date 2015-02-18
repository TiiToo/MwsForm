Preparando composer:

		En la Consola de Linux o Git Bash:
		
		a. ssh-keygen
		b. Ir al Archivo que se genero y copiar la clave publica
		c. Ir a BitBucket -> Setting (Esquina Inferior Izquierda) -> Deployment keys -> Agregar la Clave Publica
		d. Precionar boton Add
		
		e. ssh -T -i ~/.ssh/mykey git@bitbucket.org
		f. Ready to use composer update

Instalacion: 

1. Agregar al composer:

			"require": {
					"TiiToo/Webmail": "dev-master"
			},
			"repositories" : [
				{
					"type" : "vcs",
					"url" : "git@bitbucket.org:TiiToo/webmail.git"
				}
			],

			
2. Instalar Bundle:
			
					composer update TiiToo/Webmail

3. Incluir en el AppKernel

					new Sistema\WebmailBundle\SistemaWebmailBundle(),

4. Incluir en el autoload.php

					require_once "ezc/Base/base.php";
					spl_autoload_register(array("ezcBase", "autoload"));

				
5.Actualizar DB Schema:
					
					doctrine:schema:update
					
					
6. Agregar al config.yml:

		EJEMPLO PARA GMAIL CADA SERVIDOR DE CORREO TIENE SU CONFIGURACION
		
		sistema_webmail:
			  transport:
					type: smtp
					host: smtp.gmail.com
					user: "%webmail_mail%"
					password: "%webmail_password%"
					port: 465
						  options:
						  timeout: 15
						  connectionType: sslv3
						  preferredAuthMethod: LOGIN
			  sources:
					%webmail_source%:
					   host: imap.gmail.com
					   username: "%webmail_mail%"
					   password: "%webmail_password%"
					   ssl: true
					   
7. Agregar al parameters.yml
						
						webmail_mail: example@gmail.com
						webmail_nick: Example Nick
						webmail_password: examplepassword
						webmail_source: gmail
						
8. agregar al routing.yml

						sistema_webmail:
							resource: "@SistemaWebmailBundle/Controller/"
							type:     annotation
							prefix:   /
							
9. La Ruta Index es:

						/listar