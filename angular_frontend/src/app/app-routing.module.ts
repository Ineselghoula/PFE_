import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SearchBarComponent } from './search-bar/search-bar.component'; // Importez SearchBarComponent
import { RegisterComponent } from './register/register.component';
import { VerifyEmailComponent } from './verify-email/verify-email.component';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { ResendCodeComponent } from './resendcode/resendcode.component';
import { ProfileComponent } from './profile/profile.component';
import { EditProfileComponent } from './edit-profile/edit-profile.component';
import { CreateEventComponent } from './create-event/create-event.component'; import * as path from 'path';
import { OrganisateurEvenementsComponent } from './organisateur-evenements/organisateur-evenements.component';
import { UpdateEventComponent } from './update-event/update-event.component';
import { ShowAllEventsComponent } from './show-all-events/show-all-events.component';
import { ApproveOrganizersComponent } from './approve-organizers/approve-organizers.component';
import { ApproveEventsComponent } from './approve-events/approve-events.component';
import { ReserverEvenementComponent } from './reserver-evenement/reserver-evenement.component';
import { ReservationListComponent } from './reservation-list/reservation-list.component';
import { ListeReservationsComponent } from './liste-reservations/liste-reservations.component';
import { MesReservationsComponent } from './mes-reservations/mes-reservations.component';
const routes: Routes = [
  { path: '', component: HomeComponent },  
  { path: 'register', component: RegisterComponent },
  { path: 'verify-email', component: VerifyEmailComponent },
  { path: 'login', component: LoginComponent },
  { path: 'resend-code', component: ResendCodeComponent },
  { path: 'profile', component: ProfileComponent },  
  { path: 'edit-profile', component: EditProfileComponent } , 
  { path: 'create-event', component: CreateEventComponent },  
  { path: 'show-evenement', component: OrganisateurEvenementsComponent },
  { path: 'update-event/:id', component: UpdateEventComponent },
  { path: 'show-all-events', component: ShowAllEventsComponent },
  { path: 'approve-organizers', component: ApproveOrganizersComponent},
  { path: 'approve-events', component: ApproveEventsComponent},
  { path: 'resrve-event', component: ReserverEvenementComponent},
  { path:'reservation-list/:id', component: ReservationListComponent},
  { path: 'liste-reservations', component: ListeReservationsComponent},
  { path: 'mes-reservations', component: MesReservationsComponent},


  { path: '**', redirectTo: '/', pathMatch: 'full' }, 
];



@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }