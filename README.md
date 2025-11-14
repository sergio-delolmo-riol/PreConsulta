# PreConsulta
Proyecto: PreConsulta (Centro de Triaje Digital)

1. Introducción

PreConsulta es un proyecto para desarrollar una aplicación web progresiva (PWA) mobile-first destinada a digitalizar y optimizar el proceso de triaje en los servicios de urgencias hospitalarias.

El desarrollo de este proyecto se realizará en su mayor parte utilizando inteligencia artificial generativa de código. Este documento sirve como la guía conceptual y el conjunto de requisitos fundamentales para dirigir dicho desarrollo.

2. El Problema

El proceso de triaje en urgencias actual es a menudo ineficiente y propenso a errores. Se basa en registros manuales o semidigitales, lo que provoca:

Ineficiencia y Errores: Duplicación de tareas y errores de transcripción.

Falta de Trazabilidad: Dificultad para auditar las decisiones de prioridad.

Comunicación Deficiente: Alta ansiedad en pacientes y familiares por falta de información.

Sobrecarga del Personal: Estrés y carga cognitiva elevada para el personal sanitario.

3. La Solución: PreConsulta

PreConsulta abordará estos problemas mediante una plataforma intuitiva, accesible y centrada en el usuario que permita realizar un "pre-triaje" digital.

Objetivos Clave:

Reducir Tiempos: Agilizar el tiempo medio desde la admisión hasta la clasificación.

Mejorar la Accesibilidad: Garantizar el acceso universal, cumpliendo con las normativas más estrictas.

Aumentar la Transparencia: Ofrecer información clara al paciente y a los familiares sobre su estado y prioridad.

Reducir la Carga Cognitiva: Simplificar el flujo de trabajo para el personal (enfermería, celadores, médicos).

4. Perfiles de Usuario

El sistema está diseñado para tres perfiles de usuario principales, cada uno con necesidades y contextos distintos:

Paciente (y Familiares):

Contexto: Ansiedad, estrés, posible dolor, diversidad funcional (ej. daltonismo, baja visión, dislexia).

Necesidad: Un proceso de registro rápido, claro y tranquilizador. Información constante sobre su estado.

Enfermero/a de Triaje:

Contexto: Alta presión, multitarea, necesidad de tomar decisiones rápidas y precisas.

Necesidad: Información fiable y pre-registrada del paciente, alertas claras y una interfaz que reduzca la carga cognitiva.

Celador:

Contexto: Entorno ruidoso (70dB+), en constante movimiento, uso de dispositivos móviles.

Necesidad: Instrucciones de traslado claras, inequívocas y con confirmaciones multimodales (vibración, visual).

5. Pilar Fundamental: Accesibilidad (WCAG 2.1 Nivel AA)

La accesibilidad no es una característica opcional, sino el núcleo del diseño. El entorno de uso (estrés, ruido, diversidad funcional) exige el cumplimiento estricto de WCAG 2.1 Nivel AA.

Esto se traduce en:

Contraste Alto: Mínimo 4.5:1 para texto.

Soporte a la Diversidad Funcional: Modos específicos o diseños compatibles con daltonismo y baja visión.

Lectura Fácil: Uso de pictogramas, lenguaje claro y textos breves.

Navegación Semántica (ARIA): Uso extensivo de roles y atributos ARIA para lectores de pantalla.

Zonas Táctiles Amplias: Botones y controles de al menos 44x44 píxeles.

Feedback Multimodal: Confirmaciones visuales, sonoras y hápticas (vibración).

Captura Multimodal: Permitir la entrada de síntomas por voz, además de por texto.

6. Desarrollo con IA Generativa: Punto de Partida

El desarrollo se basará en prompts dirigidos a una IA generativa. El siguiente es el prompt base para la creación de la pantalla de inicio (Home), que podrá ser modificado y refinado en función de los requisitos de diseño y flujos de navegación identificados en la documentación.

Prompt Base:

Vamos a crear una pagina web, llamada PreConsulta, para movil sobre un triaje digital. Deberas de tener en cuenta en todo momento la accesibilidad mediante ARIA y respetar la WCAG 2 para lograr un nivel AA. Se te iran adjuntando imagenes sobre los diferentes mockups de las pantallas de la pagina web, para las que deberas de realizar el codigo de estas utilizando html, css, javascript y una Base de datos.


Análisis del Prompt Base y Siguientes Pasos

Este prompt inicial define la estructura básica de la aplicación para el paciente:

Botón Central (Aviso): Corresponde a la Tarea Crítica 1.1 (Comunicación de emergencia). Debe ser el foco principal de la página, permitiendo al paciente notificar al hospital su llegada o solicitar ayuda.

Navbar (Navegación):

Home: Vuelve a la pantalla del botón de aviso.

Perfil: Corresponde a la Tarea 1.2 (Consulta de información médica) y Tarea 1.3 (Gestión de citas).

Llamada de Emergencia: Una función de acceso rápido para contactar a los servicios de emergencia (ej. 112, 061).

El desarrollo continuará desde esta base para construir los demás flujos (captura de síntomas, visualización de estado de cola, etc.) y las interfaces para el personal sanitario (Enfermería, Celador), siempre respetando los principios de accesibilidad y el contexto de uso detallados.

En la carpeta media/pantallas se encuentran las diferentes pantallas que se deberan de implementar para la pagina web