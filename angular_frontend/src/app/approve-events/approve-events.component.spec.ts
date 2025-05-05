import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ApproveEventsComponent } from './approve-events.component';

describe('ApproveEventsComponent', () => {
  let component: ApproveEventsComponent;
  let fixture: ComponentFixture<ApproveEventsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ApproveEventsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ApproveEventsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
