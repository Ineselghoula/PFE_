import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NavbarComponent } from './navbar/navbar.component';
import { SearchBarComponent } from './search-bar/search-bar.component';
import { EventCardComponentComponent } from './event-card-component/event-card-component.component';
import { WhyChooseUsComponent } from './why-choose-us/why-choose-us.component';
import { AnimationCarouselComponent } from './animation-carousel/animation-carousel.component';
import { FormsModule } from '@angular/forms';
import { RegisterComponent } from './register/register.component';
import { VerifyEmailComponent } from './verify-email/verify-email.component';
import { LoginComponent } from './login/login.component';
import { AuthInterceptor } from './auth.interceptor';
import { HomeComponent } from './home/home.component'; // Importez l'intercepteur

@NgModule({
  declarations: [
    AppComponent,
    NavbarComponent,
    SearchBarComponent,
    EventCardComponentComponent,
    WhyChooseUsComponent,
    AnimationCarouselComponent,
    RegisterComponent,
    VerifyEmailComponent,
    LoginComponent,
    HomeComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    BrowserAnimationsModule,
    HttpClientModule,
    FormsModule
  ],
  providers: [
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true, // Permet d'ajouter plusieurs intercepteurs si nécessaire
    },
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }


