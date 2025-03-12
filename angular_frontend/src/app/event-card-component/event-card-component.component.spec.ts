import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EventCardComponentComponent } from './event-card-component.component';

describe('EventCardComponentComponent', () => {
  let component: EventCardComponentComponent;
  let fixture: ComponentFixture<EventCardComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ EventCardComponentComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EventCardComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
