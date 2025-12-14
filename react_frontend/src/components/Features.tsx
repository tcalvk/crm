import { Users, FileText, CreditCard, BarChart, Clock, Shield } from 'lucide-react';
import { ImageWithFallback } from './figma/ImageWithFallback';

export function Features() {
  const features = [
    {
      icon: Users,
      title: 'Customer & Contract Tracking',
      description: 'Keep all your customer information and contracts organized in one place. Track relationships, history, and important details effortlessly.',
      image: 'https://images.unsplash.com/photo-1556740758-90de374c12ad?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjdXN0b21lciUyMHNlcnZpY2UlMjBwcm9mZXNzaW9uYWx8ZW58MXx8fHwxNzY1NTI1NDAxfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral'
    },
    {
      icon: CreditCard,
      title: 'Automated Invoicing & Payments',
      description: 'Generate professional invoices in seconds and collect payments automatically. Say goodbye to manual billing and follow-ups.',
      image: 'https://images.unsplash.com/photo-1735825764457-ffdf0b5aa5dd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxpbnZvaWNlJTIwcGF5bWVudCUyMG9mZmljZXxlbnwxfHx8fDE3NjU2MDI5NjF8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral'
    },
    {
      icon: FileText,
      title: 'Contact Management',
      description: 'Store and manage all your business contacts with ease. Access contact details, communication history, and notes anytime, anywhere.',
      image: 'https://images.unsplash.com/photo-1579389248774-07907f421a6b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzbWFsbCUyMGJ1c2luZXNzJTIwdGVhbSUyMG1lZXRpbmd8ZW58MXx8fHwxNzY1NjAyOTYwfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral'
    }
  ];

  const additionalFeatures = [
    {
      icon: BarChart,
      title: 'Insights & Reports',
      description: 'Get valuable insights into your business performance with detailed analytics and reports.'
    },
    {
      icon: Clock,
      title: 'Time-Saving Automation',
      description: 'Automate repetitive tasks and focus on what matters - growing your business.'
    },
    {
      icon: Shield,
      title: 'Secure & Reliable',
      description: 'Your data is protected with enterprise-grade security and regular backups.'
    }
  ];

  return (
    <section id="features" className="bg-gray-50 py-20">
      <div className="container mx-auto px-6">
        <div className="text-center mb-16">
          <h2 className="text-gray-900 mb-4">
            Everything You Need to Run Your Business
          </h2>
          <p className="text-gray-600 max-w-2xl mx-auto">
            Our CRM platform provides all the tools small businesses need to manage customers, automate workflows, and drive growth.
          </p>
        </div>

        {/* Main Features */}
        <div className="space-y-20 mb-20">
          {features.map((feature, index) => {
            const Icon = feature.icon;
            const isEven = index % 2 === 0;
            
            return (
              <div
                key={index}
                className={`grid md:grid-cols-2 gap-12 items-center ${
                  isEven ? '' : 'md:grid-flow-dense'
                }`}
              >
                <div className={isEven ? '' : 'md:col-start-2'}>
                  <div className="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                    <Icon className="w-6 h-6 text-blue-600" />
                  </div>
                  <h3 className="text-gray-900 mb-4">
                    {feature.title}
                  </h3>
                  <p className="text-gray-600 mb-6">
                    {feature.description}
                  </p>
                  <ul className="space-y-2">
                    <li className="flex items-center gap-2 text-gray-700">
                      <div className="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
                      Easy to use interface
                    </li>
                    <li className="flex items-center gap-2 text-gray-700">
                      <div className="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
                      Real-time updates
                    </li>
                    <li className="flex items-center gap-2 text-gray-700">
                      <div className="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
                      Mobile accessible
                    </li>
                  </ul>
                </div>
                <div className={isEven ? '' : 'md:col-start-1 md:row-start-1'}>
                  <div className="aspect-[4/3] rounded-xl overflow-hidden shadow-lg">
                    <ImageWithFallback
                      src={feature.image}
                      alt={feature.title}
                      className="w-full h-full object-cover"
                    />
                  </div>
                </div>
              </div>
            );
          })}
        </div>

        {/* Additional Features Grid */}
        <div className="grid md:grid-cols-3 gap-8">
          {additionalFeatures.map((feature, index) => {
            const Icon = feature.icon;
            return (
              <div key={index} className="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div className="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                  <Icon className="w-6 h-6 text-blue-600" />
                </div>
                <h4 className="text-gray-900 mb-2">
                  {feature.title}
                </h4>
                <p className="text-gray-600">
                  {feature.description}
                </p>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
