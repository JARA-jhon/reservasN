import { Component, OnInit, ViewChild } from '@angular/core';
import {Departamento} from '../../model/departamento';
import {DepartamentoService} from '../../services/departamento.service';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {MatTableDataSource} from '@angular/material/table';
import Swal from 'sweetalert2';
import { MatSort } from '@angular/material/sort';
import { MatPaginator } from '@angular/material/paginator';

@Component({
  selector: 'app-departamento',
  templateUrl: './departamento.component.html',
  styleUrls: ['./departamento.component.css']
})
export class DepartamentoComponent implements OnInit {
  public frmDepartamentos: FormGroup;
  displayedColumns: string[] = ['idDepto', 'nomDepto']
  deptos = []
  deptoRta = Departamento;
  dataSource = new MatTableDataSource();
  editable: boolean;

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator, {static: true}) paginator: MatPaginator;

  constructor(private departamentoService: DepartamentoService, private formBuilder: FormBuilder) { 
    this.frmDepartamentos=new FormGroup({});
  }

  ngOnInit(): void {
    this.frmDepartamentos = this.formBuilder.group({
      nomDepto: ['', Validators.required]
      
    });
    this.getDepartamentos();
    this.editable = false;
  }

  getDepartamentos(){
    this.departamentoService.getDepartamentos().subscribe(
      data => {
        this.dataSource = new MatTableDataSource(data.data.deptos);
        this.dataSource.paginator = this.paginator;
        this.deptos = data.data.deptos;
      }
    );
    console.log(this.deptos);
  }

  sendData(){
    if (this.frmDepartamentos.valid) {
      let objDepto: any = {
        nomDepto: "string"
      }
      objDepto.nomDepto = this.frmDepartamentos.controls.nomDepto.value;
      this.departamentoService.insertarDepartamento(objDepto).subscribe(
        data => {
          if (data.succes) {
            this.getDepartamentos();
            this.frmDepartamentos.reset();
            this.swal('Registro Correcto!!', 'success');
          }else {
            this.swal(data.messages[0], 'danger');
          }
        }
      ), error => {
        this.swal(error, 'danger');
      }
    }
  }

  swal(mensaje, icon){
    Swal.fire({
      position: 'center',
      icon: icon,
      title: mensaje,
      showConfirmButton: false,
      timer: 2000
    })
  }

  cancelar(){
    this.frmDepartamentos.reset();
  }

}
