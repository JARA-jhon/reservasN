export class Areas {
    statusCode: number;
    success: boolean;
    messages: string[];
    data: Data;
}
class Data{
    filas: number;
    areas: Areas[];
}

class area{
    idArea: number;
    nomArea: string;
}
