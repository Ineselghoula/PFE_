import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NavbarComponent } from './navbar/navbar.component';
import { SearchBarComponent } from './search-bar/search-bar.component';
import { WhyChooseUsComponent } from './why-choose-us/why-choose-us.component';
import { AnimationCarouselComponent } from './animation-carousel/animation-carousel.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';  // ✅ Ajout de ReactiveFormsModule
import { RegisterComponent } from './register/register.component';
import { VerifyEmailComponent } from './verify-email/verify-email.component';
import { LoginComponent } from './login/login.component';
import { AuthInterceptor } from './auth.interceptor';
import { HomeComponent } from './home/home.component';
import { ResendCodeComponent } from './resendcode/resendcode.component';
import { ProfileComponent } from './profile/profile.component';
import { MatCardModule } from '@angular/material/card';
import { CommonModule } from '@angular/common';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { SidebarComponent } from './sidebar/sidebar.component';
import { EditProfileComponent } from './edit-profile/edit-profile.component';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core'; 
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { MatInputModule } from '@angular/material/input';
import { CreateEventComponent} from './create-event/create-event.component';
import { OrganisateurEvenementsComponent } from './organisateur-evenements/organisateur-evenements.component';
import { UpdateEventComponent } from './update-event/update-event.component';
import { ShowAllEventsComponent } from './show-all-events/show-all-events.component';
import { ApproveOrganizersComponent } from './approve-organizers/approve-organizers.component';
import { ApproveEventsComponent } from './approve-events/approve-events.component';
import { ReserverEvenementComponent } from './reserver-evenement/reserver-evenement.component';
import { ReservationListComponent } from './reservation-list/reservation-list.component';
import { ListeReservationsComponent } from './liste-reservations/liste-reservations.component';
import { MesReservationsComponent } from './mes-reservations/mes-reservations.component'; 



@NgModule({
  declarations: [
    AppComponent,
    NavbarComponent,
    SearchBarComponent,
    WhyChooseUsComponent,
    AnimationCarouselComponent,
    RegisterComponent,
    VerifyEmailComponent,
    LoginComponent,
    HomeComponent,
    ResendCodeComponent,
    ProfileComponent,
    SidebarComponent,
    EditProfileComponent,
    CreateEventComponent,
    OrganisateurEvenementsComponent,
    UpdateEventComponent,
    ShowAllEventsComponent,
    ApproveOrganizersComponent,
    ApproveEventsComponent,
    ReserverEvenementComponent,
    ReservationListComponent,
    ListeReservationsComponent,
    MesReservationsComponent,
  
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    BrowserAnimationsModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule,
    MatCardModule,
    CommonModule,
    MatButtonModule,
    MatIconModule,
    MatProgressSpinnerModule,
    MatFormFieldModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatSnackBarModule,
    MatInputModule,
    CommonModule,
    MatFormFieldModule,
    MatSnackBarModule,
    ReactiveFormsModule,  
    CommonModule
  ],
  providers: [
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true,
    },
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
