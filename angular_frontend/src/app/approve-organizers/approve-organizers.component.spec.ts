import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ApproveOrganizersComponent } from './approve-organizers.component';

describe('ApproveOrganizersComponent', () => {
  let component: ApproveOrganizersComponent;
  let fixture: ComponentFixture<ApproveOrganizersComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ApproveOrganizersComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ApproveOrganizersComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
