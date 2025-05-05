import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ReserverEvenementComponent } from './reserver-evenement.component';

describe('ReserverEvenementComponent', () => {
  let component: ReserverEvenementComponent;
  let fixture: ComponentFixture<ReserverEvenementComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ReserverEvenementComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ReserverEvenementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
