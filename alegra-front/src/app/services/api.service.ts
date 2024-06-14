import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {

  private apiUrl = 'http://localhost:8081/api/testAngular';
  constructor(private http: HttpClient) { }

  getTestMessage(): Observable<any> {
    const headers = new HttpHeaders({
      'x-api-key': 'your-secret-api-key'
    });

    return this.http.get<any>(this.apiUrl, { headers });
  }
}
