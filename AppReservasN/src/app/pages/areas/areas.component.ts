import { Component, OnInit, ViewChild } from '@angular/core';
import {Areas} from '../../model/areas';
import {AreasService} from '../../services/areas.service';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {MatTableDataSource} from '@angular/material/table';
import Swal from 'sweetalert2';
import { MatSort } from '@angular/material/sort';
import { MatPaginator } from '@angular/material/paginator';

@Component({
  selector: 'app-areas',
  templateUrl: './areas.component.html',
  styleUrls: ['./areas.component.css']
})
export class AreasComponent implements OnInit {

  public frmAreas: FormGroup;
  displayedColumns: string[] = ['idArea', 'nomArea']
  areas = []
  areaRta = Areas;
  dataSource = new MatTableDataSource();
  editable: boolean;

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator, {static: true}) paginator: MatPaginator;

  constructor(private areasService: AreasService, private formBuilder: FormBuilder) { 
    this.frmAreas=new FormGroup({});
  }

  ngOnInit(): void {
    this.frmAreas = this.formBuilder.group({
      nomArea: ['', Validators.required]
      
    });
    this.getAreas();
    this.editable = false;
  }

  getAreas(){
    this.areasService.getAreas().subscribe(
      data => {
        this.dataSource = new MatTableDataSource(data.data.areas);
        this.dataSource.paginator = this.paginator;
        this.areas = data.data.areas;
      }
    );
    console.log(this.areas);
  }

  sendData(){
    if (this.frmAreas.valid) {
      let objArea: any = {
        nomArea: "string"
      }
      objArea.nomArea = this.frmAreas.controls.nomArea.value;
      this.areasService.insertarAreas(objArea).subscribe(
        data => {
          if (data.succes) {
            this.getAreas();
            this.frmAreas.reset();
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
    this.frmAreas.reset();
  }

}
