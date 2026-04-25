#!/bin/bash

# Colores para mejor visualización
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuración
CONTAINER_NAME="myXampp"
PORT="41062"
VOLUME_PATH="/home/mskj/Descargas/proyecto/sigec:/opt/lampp/htdocs"
IMAGE="tomsik68/xampp:8"

# Función para mostrar ayuda
show_help() {
    echo -e "${BLUE}=== Gestor de XAMPP Docker ===${NC}"
    echo ""
    echo -e "${GREEN}Uso: $0 [comando]${NC}"
    echo ""
    echo "Comandos disponibles:"
    echo "  start       - Iniciar el contenedor"
    echo "  stop        - Detener el contenedor"
    echo "  restart     - Reiniciar el contenedor"
    echo "  status      - Ver estado del contenedor"
    echo "  logs        - Ver logs del contenedor"
    echo "  shell       - Acceder al bash del contenedor"
    echo "  create      - Crear el contenedor (si no existe)"
    echo "  recreate    - Eliminar y crear nuevamente el contenedor"
    echo "  delete      - Eliminar el contenedor"
    echo "  ls          - Listar archivos en htdocs"
    echo "  help        - Mostrar esta ayuda"
    echo ""
}

# Función para verificar si el contenedor existe
container_exists() {
    docker ps -a --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"
}

# Función para verificar si el contenedor está corriendo
container_running() {
    docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"
}

# Comando: start
cmd_start() {
    if container_exists; then
        if container_running; then
            echo -e "${YELLOW}El contenedor ya está corriendo${NC}"
        else
            echo -e "${GREEN}Iniciando contenedor...${NC}"
            docker start $CONTAINER_NAME
            echo -e "${GREEN}Contenedor iniciado. Accede en: http://localhost:$PORT/${NC}"
        fi
    else
        echo -e "${RED}El contenedor no existe. Ejecuta: $0 create${NC}"
    fi
}

# Comando: stop
cmd_stop() {
    if container_exists && container_running; then
        echo -e "${GREEN}Deteniendo contenedor...${NC}"
        docker stop $CONTAINER_NAME
        echo -e "${GREEN}Contenedor detenido${NC}"
    else
        echo -e "${RED}El contenedor no está corriendo o no existe${NC}"
    fi
}

# Comando: restart
cmd_restart() {
    if container_exists; then
        echo -e "${GREEN}Reiniciando contenedor...${NC}"
        docker restart $CONTAINER_NAME
        echo -e "${GREEN}Contenedor reiniciado. Accede en: http://localhost:$PORT/${NC}"
    else
        echo -e "${RED}El contenedor no existe. Ejecuta: $0 create${NC}"
    fi
}

# Comando: status
cmd_status() {
    if container_exists; then
        echo -e "${BLUE}Estado del contenedor:${NC}"
        docker ps -a --filter "name=$CONTAINER_NAME" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
    else
        echo -e "${RED}El contenedor no existe${NC}"
    fi
}

# Comando: logs
cmd_logs() {
    if container_exists; then
        docker logs $CONTAINER_NAME
    else
        echo -e "${RED}El contenedor no existe${NC}"
    fi
}

# Comando: shell
cmd_shell() {
    if container_exists && container_running; then
        echo -e "${GREEN}Accediendo al contenedor...${NC}"
        docker exec -it $CONTAINER_NAME bash
    else
        echo -e "${RED}El contenedor no está corriendo${NC}"
    fi
}

# Comando: create
cmd_create() {
    if container_exists; then
        echo -e "${YELLOW}El contenedor ya existe. Usa 'recreate' para volver a crearlo${NC}"
    else
        echo -e "${GREEN}Creando nuevo contenedor...${NC}"
        docker run --name $CONTAINER_NAME -p ${PORT}:80 -d -v $VOLUME_PATH $IMAGE
        echo -e "${GREEN}Contenedor creado exitosamente${NC}"
        echo -e "${GREEN}Accede en: http://localhost:$PORT/${NC}"
    fi
}

# Comando: recreate
cmd_recreate() {
    echo -e "${YELLOW}Eliminando contenedor existente...${NC}"
    if container_exists; then
        docker rm -f $CONTAINER_NAME
    fi
    echo -e "${GREEN}Creando nuevo contenedor...${NC}"
    docker run --name $CONTAINER_NAME -p ${PORT}:80 -d -v $VOLUME_PATH $IMAGE
    echo -e "${GREEN}Contenedor recreado exitosamente${NC}"
    echo -e "${GREEN}Accede en: http://localhost:$PORT/${NC}"
}

# Comando: delete
cmd_delete() {
    if container_exists; then
        echo -e "${RED}¿Eliminar contenedor $CONTAINER_NAME? (s/n): ${NC}"
        read -r confirm
        if [ "$confirm" = "s" ] || [ "$confirm" = "S" ]; then
            docker rm -f $CONTAINER_NAME
            echo -e "${GREEN}Contenedor eliminado${NC}"
        else
            echo -e "${YELLOW}Operación cancelada${NC}"
        fi
    else
        echo -e "${RED}El contenedor no existe${NC}"
    fi
}

# Comando: ls (listar archivos)
cmd_ls() {
    if container_exists && container_running; then
        echo -e "${BLUE}Archivos en /opt/lampp/htdocs:${NC}"
        docker exec $CONTAINER_NAME ls -la /opt/lampp/htdocs/
    else
        echo -e "${RED}El contenedor no está corriendo${NC}"
    fi
}

# Procesar comandos
case "$1" in
    start)
        cmd_start
        ;;
    stop)
        cmd_stop
        ;;
    restart)
        cmd_restart
        ;;
    status)
        cmd_status
        ;;
    logs)
        cmd_logs
        ;;
    shell)
        cmd_shell
        ;;
    create)
        cmd_create
        ;;
    recreate)
        cmd_recreate
        ;;
    delete)
        cmd_delete
        ;;
    ls)
        cmd_ls
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        echo -e "${RED}Comando no reconocido: $1${NC}"
        show_help
        exit 1
        ;;
esac
