</main>

<footer style="
    background: #1f1f1f; 
    color: #e5e5e5; 
    padding: 40px 0; 
    margin-top: 50px;
    border-top: 3px solid var(--primary-color);
">
    <div class="container" style="
        max-width: 1100px; 
        margin: auto; 
        padding: 0 20px; 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
        gap: 30px;
    ">

        <!-- Columna 1 -->
        <div>
            <h3 style="margin-bottom: 15px;">Adopta un Amigo</h3>
            <p style="line-height: 1.6;">
                Plataforma creada por estudiantes con el objetivo de facilitar la adopción 
                de mascotas de forma responsable.
            </p>
        </div>

        <!-- Columna 2 -->
        <div>
            <h3 style="margin-bottom: 15px;">Navegación</h3>
            <ul style="list-style: none; padding: 0; line-height: 1.8;">
                <li><a href="/proyecto_adopcion/public/index.php" style="color:#e5e5e5; text-decoration:none;">Inicio</a></li>
                <li><a href="/proyecto_adopcion/public/catalogo.php" style="color:#e5e5e5; text-decoration:none;">Catálogo</a></li>
                <li><a href="/proyecto_adopcion/public/login.php" style="color:#e5e5e5; text-decoration:none;">Ingresar</a></li>
                <li><a href="/proyecto_adopcion/public/registro.php" style="color:#e5e5e5; text-decoration:none;">Registrarse</a></li>
            </ul>
        </div>

        <!-- Columna 3 -->
        <div>
            <h3 style="margin-bottom: 15px;">Contacto</h3>
            <p style="line-height: 1.6;">
                Email: contacto@adoptaunamigo.com<br>
                Teléfono: 55-0000-0000<br>
                Ciudad de México
            </p>
        </div>

    </div>

    <div style="text-align:center; margin-top:30px; font-size: 0.9rem; opacity: 0.8;">
        © <?= date('Y'); ?> Adopta un Amigo. Todos los derechos reservados.
    </div>
</footer>

<script src="/proyecto_adopcion/assets/js/scripts.js"></script>

</body>
</html>
