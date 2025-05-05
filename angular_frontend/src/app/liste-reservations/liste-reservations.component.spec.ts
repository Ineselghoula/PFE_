import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListeReservationsComponent } from './liste-reservations.component';

describe('ListeReservationsComponent', () => {
  let component: ListeReservationsComponent;
  let fixture: ComponentFixture<ListeReservationsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ListeReservationsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListeReservationsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
