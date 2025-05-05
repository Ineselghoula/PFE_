import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OrganisateurEvenementsComponent } from './organisateur-evenements.component';

describe('OrganisateurEvenementsComponent', () => {
  let component: OrganisateurEvenementsComponent;
  let fixture: ComponentFixture<OrganisateurEvenementsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ OrganisateurEvenementsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OrganisateurEvenementsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
