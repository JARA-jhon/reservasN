export class Departamento {
    statusCode: number;
    success: boolean;
    messages: string[];
    data: Data;
}
class Data{
    filas: number;
    deptos: Deptos[];
}

class Deptos{
    idDepto: number;
    nomDepto: string;
}
