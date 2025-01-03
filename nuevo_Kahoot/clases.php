<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        class usuarios{
            private $bd;
            private $nombre;
            private $tEmpieza;
            private $tFinal;

            public function __construct($db,$nom="", $tEmp="", $tFin=""){
                $this->bd=$db;
                $this->nombre=$nom;
                $this->tEmpieza=$tEmp;
                $this->tFinal=$tFin;
            }


            public function insertarUsuarioTiempo(){
                if($this->check_usuario($this->nombre)){
                    $sent="INSERT INTO usuarios(nombre) VALUES (?);";//No hay que pasarle dos parámetros ?,? haciendo referencia la nombre y al tiempo en el que empieza, porque el tiempo, al estar predeterminado en la bd como current_timesptamp(), va a manejarlo automáticamente MySQL, es decir, lo va a insertar automáticamente

                    $cons=$this->bd->prepare($sent);
                    // $t = time();
                    $cons->bind_param("s",$this->nombre);
                    $cons->execute();


                    $cons->close();

                    // header("Location:cuestionario.php?mensaje=1");
                    // exit(); //El exit() sirve para asegurarse de que el script se detenga y no siga ejecutandose
                }else{
                    header("Location:index.php?mensaje=2");
                    exit();
                }
                
            }




            public function check_usuario($inputNom){

                $sent="SELECT count(nombre) FROM usuarios WHERE nombre=?;"; //? indica que es un paraámetro dinámico, es decir, que será introducido más tarde y que puede ser un nombre diferente cada vez que se ejecute la consulta

                try{
                    $cons=$this->bd->prepare($sent);//Crea un objeto preparado basado en la consulta(sent)
                    $cons->bind_param("s",$inputNom);//Reemplaza ? en la consulta por el valor real del nombre, en este caso, el parámetro pasado al método($inputNom)
                    try{
                        $cons->execute();//Se ejecuta la consulta preparada
                    }catch(Exception $e){
                        echo "Error al ejecutar la consulta para comprobar si el usuario ya existe en la bd";
                    }

                    $cons->bind_result($cont);//Asocia el resultado de la ejecución de la consulta con la variable $cont, es decir, en esa variable se va almacenar el resultado (El resultado será una fila)

                    $cons->fetch(); //Como el o los resultados siempre se devuelven en una o varias filas, fetch recupera esa fila y la coloca en la variable vinculada, en este caso $cont. En el caso de devolver varias filas, habría que hacer un bucle while (mientras que saque resultados, estos se van almacenando)
                    
                    
                    //Lo que devuelva el método lo utilizaremos a su vez en otro método (Llamando a esta función) para saber si tenemos que insertar o no el usuario
                    //Si el nombre no existe en la bd, significa que se podrá insertar al usuario, y por eso devuelve true, si devuelve 1, significará que el usuario no existe y por tanto no se puede insertar
                    if($cont==0){
                        return true;
                    }else{
                        return false;
                    }
                
                }catch(Exception $e){
                    echo "Error al comprobar si el usuario ya existe en la bd";
                }finally{
                    if(isset($cons)){
                        $cons->close(); //Se cierra la consulta para liberar los recursos
                    }
                }              
            }



            public function __toString(){
                $str = " <br>".$this->nombre;
                return $str;
            }
        }





        class preguntas{
            private $bd;
            private $cod;
            private $enunciado;
            private $respuesta;
            // private $preguntasMostradas
            // $preguntasMostradas=[];
            // $pregunta = obtenerPreguntaAleatoria($preguntasMostradas); // Función que busca una pregunta

            public function __construct($db,$cod="",$pM="", $enun="", $res=""){
                $this->bd=$db;
                $this->cod=$cod;
                // $this->preguntasMostradas=$pM;
                $this->enunciado=$enun;
                $this->respuesta=$res;
            }


            // public function get_pregunta(){
            //     //Generar un número random
            //     $sent="SELECT MAX(cod) from preguntas;";//Consultar cuantas preguntas hay en la bd para saber el límite máximo del num random
            //     $cons=$this->bd->prepare($sent);
            //     $cons->execute();
            //     $cons->bind_result($codMax);
            //     $cons->fetch();
            //     $cons->close();

            //     if($codMax<1){//Comprobar que haya preguntas en la bd
            //         echo "No hay preguntas en la base de datos";
            //     }else{
                    
            //         //El número se sigue generando hasta que no esté incluido dentro del array de las preguntas que ya se han mostrado
            //         do{
            //             $codRandom=random_int(1,$codMax);//Generar el número random
            //         }while(str_contains($this->preguntasMostradas,$codRandom));//Se comprueba si el número generado está dentro del string con las preguntas mostradas, si devuelve true es que ya existe y por lo tanto se tiene qie generar otro número
                    
                    


            //         //Generar la consulta del enunciado a partir del num random
            //         $sent="SELECT cod,enunciado FROM preguntas WHERE cod=?;";//El código es dinámico

            //         $cons=$this->bd->prepare($sent);
            //         $cons->bind_param("i",$codRandom);
            //         $cons->execute();
            //         $cons->bind_result($this->cod, $this->enunciado);

            //         $cons->fetch();
            //         // $this->preguntas_mostradas = $codRandom." ";//Añadir el código al array para comprobar que no se repita

            //         $cons->close(); 
            //         // Retornar el objeto con echo para mostrarlo con __toString()
            //         echo $this;
            //     }
            // }

            // Esta función genera una pregunta aleatoria que no haya sido mostrada
            public function generar_preg_Aleatoria($p_excluidas) {
                // Convertimos el array de preguntas mostradas en una lista separada por comas para SQL
                // $excluir = implode(",", $p_excluidas);
                // $sql = "SELECT cod, enunciado FROM preguntas WHERE cod NOT IN ($excluir) ORDER BY RAND() LIMIT 1";
                // $resultado = $this->bd->query($sql);
                // $pregunta = $resultado->fetch_assoc(); // Recupera la pregunta
        
                // // Si no hay más preguntas para mostrar
                // if (!$pregunta) {
                //     echo "No hay más preguntas disponibles.";
                //     exit;
                // }
        
                // return $pregunta;
                // Si el array está vacío, asigna un valor que no cause errores en la consulta
                if (empty($excluidas)) {
                    $sql = "SELECT cod, enunciado FROM preguntas ORDER BY RAND() LIMIT 1";
                } else {
                    $excluir = implode(",", $excluidas);  // Convierte el array en una cadena de valores
                    $sql = "SELECT cod, enunciado FROM preguntas WHERE cod NOT IN ($excluir) ORDER BY RAND() LIMIT 1";
                }

                // Ejecuta la consulta
                $resultado = $this->bd->query($sql);
                $pregunta = $resultado->fetch_assoc();
                
                return $pregunta;
            }


            public function comprobarRespuesta($resUsuario) {
                $sent = "SELECT respuesta FROM preguntas WHERE cod = ?;";
                try {
                    $cons = $this->bd->prepare($sent);
                    $cons->bind_param("i", $this->cod);
                    $cons->execute();
                    $cons->bind_result($resBd);
                    $cons->fetch();
        
                    // Comparamos la respuesta del usuario con la de la base de datos
                    if (strtolower(trim($resBd)) == strtolower(trim($resUsuario))) {
                        // Si la respuesta es correcta, mostramos la siguiente pregunta
                        return true;
                    } else {
                        // Si es incorrecta, repetimos la misma pregunta
                        return false;
                    }
                } catch (Exception $e) {
                    echo "Error al comprobar la respuesta.";
                }
            }



             // Esta función muestra la pregunta en un formulario
            public function mostrar_pregunta($pregunta) {
                // Creamos el formulario con la pregunta aleatoria y el código de la misma
                $str = '<form action="#" method="post">
                            <p>' . $pregunta['enunciado'] . '</p>
                            <input type="hidden" name="codPA" value="' . $pregunta['cod'] . '">
                            <input type="hidden" name="pMostradas" value="' . implode(',', $_POST['pMostradas'] ?? []) . ',' . $pregunta['cod'] . '">
                            <input type="text" name="res" required><br>
                            <input type="submit" value="Enviar" name="env1">
                        </form>';
                return $str;
            }
            
        }
    ?>
</body>
</html>